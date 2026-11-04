<?php

namespace App\Filament\Widgets;

use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TicketStatsOverview extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = -5;

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        /** @var User|null $user */
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        // 1. Super Administrator
        if ($user->isSuperAdmin()) {
            $total = Ticket::count();
            $open = Ticket::open()->count();
            $overdue = Ticket::overdue()->count();
            $reopened = Ticket::where('status', 'reopened')->count();
            $closed = Ticket::where('status', 'closed')->count();
            $depts = Department::where('is_active', true)->count();

            return [
                Stat::make('Total Organizations Complaints', (string) $total)
                    ->description("Across {$depts} active departments")
                    ->color('primary')
                    ->icon('heroicon-o-ticket'),
                Stat::make('Active Work in Progress', (string) $open)
                    ->description('Currently being addressed')
                    ->color('info')
                    ->icon('heroicon-o-arrow-path'),
                Stat::make('Overdue / SLA Alerts', (string) $overdue)
                    ->description($overdue > 0 ? 'Exceeded SLA deadline' : 'All SLAs within targets')
                    ->color($overdue > 0 ? 'danger' : 'success')
                    ->icon('heroicon-o-exclamation-triangle'),
                Stat::make('Reopened Issues', (string) $reopened)
                    ->description('Reported as not resolved')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-uturn-left'),
                Stat::make('Confirmed Resolved', (string) $closed)
                    ->description('Confirmed by complainants')
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),
            ];
        }

        // 2. IT HOD
        if ($user->isItHod()) {
            $unassigned = Ticket::unassigned()->count();
            $inProgress = Ticket::where('status', 'in_progress')->count();
            $overdue = Ticket::overdue()->count();
            $reopened = Ticket::where('status', 'reopened')->count();
            $awaitingConfirm = Ticket::where('status', 'awaiting_confirmation')->count();

            return [
                Stat::make('Unassigned Incoming Queue', (string) $unassigned)
                    ->description('Requires technician assignment')
                    ->color($unassigned > 0 ? 'danger' : 'success')
                    ->icon('heroicon-o-inbox-arrow-down'),
                Stat::make('Active In-Progress', (string) $inProgress)
                    ->description('Under active investigation')
                    ->color('info')
                    ->icon('heroicon-o-wrench-screwdriver'),
                Stat::make('Overdue Complaints', (string) $overdue)
                    ->description('SLA resolution breached')
                    ->color($overdue > 0 ? 'danger' : 'success')
                    ->icon('heroicon-o-clock'),
                Stat::make('Reopened by Users', (string) $reopened)
                    ->description('Requires review/reassignment')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-path'),
                Stat::make('Awaiting User Confirmation', (string) $awaitingConfirm)
                    ->description('Work marked completed')
                    ->color('purple')
                    ->icon('heroicon-o-check-badge'),
            ];
        }

        // 3. IT Support Technician
        if ($user->isItTechnician()) {
            $myAssigned = Ticket::where('assigned_technician_id', $user->id)->whereNotIn('status', ['closed', 'cancelled'])->count();
            $myInProgress = Ticket::where('assigned_technician_id', $user->id)->where('status', 'in_progress')->count();
            $myOverdue = Ticket::where('assigned_technician_id', $user->id)->overdue()->count();
            $myCompleted = Ticket::where('assigned_technician_id', $user->id)->whereIn('status', ['awaiting_confirmation', 'closed'])->count();

            return [
                Stat::make('My Active Assignments', (string) $myAssigned)
                    ->description('Tickets assigned to me')
                    ->color('primary')
                    ->icon('heroicon-o-clipboard-document-list'),
                Stat::make('Work In Progress', (string) $myInProgress)
                    ->description('Currently working on')
                    ->color('info')
                    ->icon('heroicon-o-play'),
                Stat::make('My Overdue Tasks', (string) $myOverdue)
                    ->description($myOverdue > 0 ? 'Requires immediate action' : 'On schedule')
                    ->color($myOverdue > 0 ? 'danger' : 'success')
                    ->icon('heroicon-o-exclamation-circle'),
                Stat::make('Resolved by Me', (string) $myCompleted)
                    ->description('Completed assignments')
                    ->color('success')
                    ->icon('heroicon-o-check-circle'),
            ];
        }

        // 4. Department HOD
        if ($user->isDepartmentHod()) {
            $deptTotal = Ticket::where('department_id', $user->department_id)->count();
            $deptOpen = Ticket::where('department_id', $user->department_id)->open()->count();
            $deptAwaiting = Ticket::where('department_id', $user->department_id)->where('status', 'awaiting_confirmation')->count();
            $deptClosed = Ticket::where('department_id', $user->department_id)->where('status', 'closed')->count();

            return [
                Stat::make('Department Total Complaints', (string) $deptTotal)
                    ->description($user->department?->name ?? 'Department')
                    ->color('primary')
                    ->icon('heroicon-o-building-office-2'),
                Stat::make('Active In-Progress', (string) $deptOpen)
                    ->description('Being resolved by IT')
                    ->color('info')
                    ->icon('heroicon-o-clock'),
                Stat::make('Awaiting Confirmation', (string) $deptAwaiting)
                    ->description('Employees need to confirm resolution')
                    ->color('warning')
                    ->icon('heroicon-o-hand-raised'),
                Stat::make('Successfully Resolved', (string) $deptClosed)
                    ->description('Completed tickets')
                    ->color('success')
                    ->icon('heroicon-o-check-badge'),
            ];
        }

        // 5. General Employee / Complainant
        $myTotal = Ticket::where('complainant_id', $user->id)->count();
        $myOpen = Ticket::where('complainant_id', $user->id)->open()->count();
        $myAwaitingConfirm = Ticket::where('complainant_id', $user->id)->where('status', 'awaiting_confirmation')->count();
        $myClosed = Ticket::where('complainant_id', $user->id)->where('status', 'closed')->count();

        return [
            Stat::make('My Total Complaints', (string) $myTotal)
                ->description('All lodged complaints')
                ->color('primary')
                ->icon('heroicon-o-ticket'),
            Stat::make('Active Complaints', (string) $myOpen)
                ->description('Under investigation by IT')
                ->color('info')
                ->icon('heroicon-o-arrow-path'),
            Stat::make('Awaiting My Confirmation', (string) $myAwaitingConfirm)
                ->description($myAwaitingConfirm > 0 ? 'Please confirm or reopen' : 'None pending')
                ->color($myAwaitingConfirm > 0 ? 'warning' : 'gray')
                ->icon('heroicon-o-exclamation-circle'),
            Stat::make('Confirmed Resolved', (string) $myClosed)
                ->description('Closed complaints')
                ->color('success')
                ->icon('heroicon-o-check-badge'),
        ];
    }
}
