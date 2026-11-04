<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Filament\Widgets\Widget;

class NoticeableAlertsWidget extends Widget
{
    protected string $view = 'filament.widgets.noticeable-alerts-widget';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected static ?int $sort = -3;

    protected function getViewData(): array
    {
        /** @var User|null $user */
        $user = auth()->user();

        $ticketQuery = Ticket::query();
        if ($user) {
            $ticketQuery->accessibleBy($user);
        }

        // Critical / Overdue tickets
        $overdueTickets = (clone $ticketQuery)->overdue()->get();
        $criticalActiveTickets = (clone $ticketQuery)
            ->where('priority', 'critical')
            ->whereNotIn('status', ['closed', 'cancelled'])
            ->get();

        $unassignedCount = (clone $ticketQuery)->unassigned()->count();

        // Calculate SLA compliance rate
        $closedTickets = (clone $ticketQuery)->where('status', 'closed')->count();
        $breachedClosed = (clone $ticketQuery)->where('status', 'closed')->where('is_sla_resolution_breached', true)->count();
        $slaRate = $closedTickets > 0 
            ? round((($closedTickets - $breachedClosed) / $closedTickets) * 100, 1) 
            : 100;

        // Technicians on duty
        $techniciansCount = User::where('role', 'it_technician')->where('is_active', true)->count();

        return [
            'overdueCount' => $overdueTickets->count(),
            'criticalCount' => $criticalActiveTickets->count(),
            'unassignedCount' => $unassignedCount,
            'slaRate' => $slaRate,
            'techniciansCount' => $techniciansCount,
            'overdueTickets' => $overdueTickets->take(3),
            'criticalTickets' => $criticalActiveTickets->take(3),
        ];
    }
}

