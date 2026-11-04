<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportExportService
{
    /**
     * Build filtered ticket query based on filters and user authorization.
     */
    public function getFilteredQuery(array $filters, User $user): Builder
    {
        $query = Ticket::query()->with(['department', 'category', 'complainant', 'assignedTechnician']);

        // Enforce user authorization scope
        $query->accessibleBy($user);

        // Apply filters
        if (!empty($filters['from_date'])) {
            $query->whereDate('submitted_at', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('submitted_at', '<=', $filters['to_date']);
        }

        if (!empty($filters['department_id'])) {
            // If user is departmental HOD, restrict to their department only
            if ($user->isDepartmentHod() && !$user->isItHod()) {
                $query->where('department_id', $user->department_id);
            } else {
                $query->where('department_id', $filters['department_id']);
            }
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['assigned_technician_id'])) {
            $query->where('assigned_technician_id', $filters['assigned_technician_id']);
        }

        return $query->latest('submitted_at');
    }

    /**
     * Export to CSV streamed response.
     */
    public function exportCsv(array $filters, User $user): StreamedResponse
    {
        $tickets = $this->getFilteredQuery($filters, $user)->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="it-helpdesk-report-' . now()->format('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($tickets) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Tracking ID',
                'Title',
                'Department',
                'Category',
                'Priority',
                'Status',
                'Complainant',
                'Assigned Technician',
                'Submitted At (PKT)',
                'Assigned At (PKT)',
                'Processed At (PKT)',
                'Completed At (PKT)',
                'Final Result / Resolution Summary',
                'SLA Resolution Breached',
            ]);

            foreach ($tickets as $ticket) {
                fputcsv($file, [
                    $ticket->tracking_id,
                    $ticket->title,
                    $ticket->department?->name ?? 'N/A',
                    $ticket->category?->name ?? 'N/A',
                    ucfirst($ticket->priority),
                    ucwords(str_replace('_', ' ', $ticket->status)),
                    $ticket->complainant?->name ?? 'N/A',
                    $ticket->assignedTechnician?->name ?? 'Unassigned',
                    $ticket->submitted_at?->timezone('Asia/Karachi')->format('d/m/Y h:i A') ?? '',
                    $ticket->assigned_at?->timezone('Asia/Karachi')->format('d/m/Y h:i A') ?? 'Not Assigned',
                    $ticket->work_started_at?->timezone('Asia/Karachi')->format('d/m/Y h:i A') ?? 'Pending Start',
                    $ticket->completed_at?->timezone('Asia/Karachi')->format('d/m/Y h:i A') ?? ($ticket->confirmed_resolved_at?->timezone('Asia/Karachi')->format('d/m/Y h:i A') ?? 'In Progress'),
                    $ticket->resolution_summary ?? ($ticket->status === 'closed' ? 'Confirmed Resolved' : 'Pending Resolution'),
                    $ticket->is_sla_resolution_breached ? 'Yes' : 'No',
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }

    /**
     * Export to Excel (.xlsx) file.
     */
    public function exportExcel(array $filters, User $user): StreamedResponse
    {
        $tickets = $this->getFilteredQuery($filters, $user)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('IT Complaints Report');

        // Header row
        $headers = [
            'A1' => 'Tracking ID',
            'B1' => 'Title',
            'C1' => 'Department',
            'D1' => 'Category',
            'E1' => 'Priority',
            'F1' => 'Status',
            'G1' => 'Complainant',
            'H1' => 'Assigned Technician',
            'I1' => 'Submitted At (PKT)',
            'J1' => 'Assigned At (PKT)',
            'K1' => 'Processed At (PKT)',
            'L1' => 'Completed At (PKT)',
            'M1' => 'Final Result / Resolution Summary',
            'N1' => 'SLA Breached',
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:N1')->applyFromArray($headerStyle);

        $row = 2;
        foreach ($tickets as $ticket) {
            $sheet->setCellValue("A{$row}", $ticket->tracking_id);
            $sheet->setCellValue("B{$row}", $ticket->title);
            $sheet->setCellValue("C{$row}", $ticket->department?->name ?? 'N/A');
            $sheet->setCellValue("D{$row}", $ticket->category?->name ?? 'N/A');
            $sheet->setCellValue("E{$row}", ucfirst($ticket->priority));
            $sheet->setCellValue("F{$row}", ucwords(str_replace('_', ' ', $ticket->status)));
            $sheet->setCellValue("G{$row}", $ticket->complainant?->name ?? 'N/A');
            $sheet->setCellValue("H{$row}", $ticket->assignedTechnician?->name ?? 'Unassigned');
            $sheet->setCellValue("I{$row}", $ticket->submitted_at?->timezone('Asia/Karachi')->format('d/m/Y h:i A') ?? '');
            $sheet->setCellValue("J{$row}", $ticket->assigned_at?->timezone('Asia/Karachi')->format('d/m/Y h:i A') ?? 'Not Assigned');
            $sheet->setCellValue("K{$row}", $ticket->work_started_at?->timezone('Asia/Karachi')->format('d/m/Y h:i A') ?? 'Pending Start');
            $sheet->setCellValue("L{$row}", $ticket->completed_at?->timezone('Asia/Karachi')->format('d/m/Y h:i A') ?? ($ticket->confirmed_resolved_at?->timezone('Asia/Karachi')->format('d/m/Y h:i A') ?? 'In Progress'));
            $sheet->setCellValue("M{$row}", $ticket->resolution_summary ?? ($ticket->status === 'closed' ? 'Confirmed Resolved' : 'Pending Resolution'));
            $sheet->setCellValue("N{$row}", $ticket->is_sla_resolution_breached ? 'Yes' : 'No');
            $row++;
        }

        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'it-helpdesk-report-' . now()->format('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Export to PDF report.
     */
    public function exportPdf(array $filters, User $user): StreamedResponse
    {
        $tickets = $this->getFilteredQuery($filters, $user)->get();

        $pdf = Pdf::loadView('reports.tickets-pdf', [
            'tickets' => $tickets,
            'filters' => $filters,
            'user' => $user,
            'generatedAt' => now()->timezone('Asia/Karachi')->format('d M Y, h:i A') . ' PKT',
        ]);

        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'it-helpdesk-report-' . now()->format('Y-m-d') . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Printable Individual Ticket Report.
     */
    public function generateTicketPdf(Ticket $ticket): StreamedResponse
    {
        $ticket->load(['department', 'category', 'subcategory', 'complainant', 'assignedTechnician', 'statusHistories.changedBy', 'comments.user', 'feedback']);

        $pdf = Pdf::loadView('reports.ticket-individual-pdf', [
            'ticket' => $ticket,
            'generatedAt' => now()->format('d M Y, h:i A'),
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "ticket-{$ticket->tracking_id}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
