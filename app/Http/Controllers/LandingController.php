<?php

namespace App\Http\Controllers;

use App\Models\ComplaintCategory;
use App\Models\Department;
use App\Models\KnowledgeArticle;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $categories = ComplaintCategory::where('is_active', true)
            ->withCount('tickets')
            ->orderBy('sort_order')
            ->get();

        $articles = KnowledgeArticle::where('is_published', true)
            ->with(['category', 'author'])
            ->latest()
            ->take(4)
            ->get();

        $stats = [
            'total_complaints' => Ticket::count(),
            'resolved_complaints' => Ticket::where('status', 'closed')->count(),
            'active_departments' => Department::where('is_active', true)->count(),
            'active_technicians' => 12, // IT operations coverage
        ];

        return view('welcome', compact('categories', 'articles', 'stats'));
    }

    public function track(Request $request): JsonResponse
    {
        $request->validate([
            'tracking_id' => 'required|string|max:40',
        ]);

        $trackingId = trim((string) $request->input('tracking_id'));

        $ticket = Ticket::where('tracking_id', $trackingId)
            ->with(['department', 'category', 'assignedTechnician', 'statusHistories' => function ($q) {
                $q->where('is_visible_to_complainant', true)->latest();
            }])
            ->first();

        if (!$ticket) {
            return response()->json([
                'found' => false,
                'message' => "No complaint found matching Tracking ID '{$trackingId}'. Please check the format (e.g. ITHN-2026-000001).",
            ], 404);
        }

        $timeline = $ticket->statusHistories->map(function ($h) {
            return [
                'action' => $h->action_label,
                'comment' => $h->comment,
                'actor' => $h->changedBy?->name ?? 'System',
                'date' => $h->created_at->format('d M Y, h:i A'),
                'time_ago' => $h->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'found' => true,
            'ticket' => [
                'id' => $ticket->id,
                'tracking_id' => $ticket->tracking_id,
                'title' => $ticket->title,
                'department' => $ticket->department?->name ?? 'N/A',
                'category' => $ticket->category?->name ?? 'N/A',
                'priority' => ucfirst($ticket->priority),
                'status' => $ticket->status,
                'status_label' => ucwords(str_replace('_', ' ', $ticket->status)),
                'status_color' => $ticket->status_badge_color,
                'technician' => $ticket->assignedTechnician ? "{$ticket->assignedTechnician->name} ({$ticket->assignedTechnician->designation})" : 'Assigned queue pending review',
                'submitted_at' => $ticket->submitted_at?->format('d M Y, h:i A') ?? 'N/A',
                'work_started_at' => $ticket->work_started_at?->format('d M Y, h:i A') ?? 'Not started yet',
                'completed_at' => $ticket->completed_at?->format('d M Y, h:i A') ?? 'In progress',
                'resolution_summary' => $ticket->resolution_summary,
                'sla_resolution_deadline' => $ticket->sla_resolution_deadline?->format('d M Y, h:i A') ?? 'N/A',
                'portal_url' => url('/admin/tickets/' . $ticket->id),
                'timeline' => $timeline,
            ],
        ]);
    }
}
