<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketEventNotification;
use Illuminate\Support\Collection;
use Throwable;

class NotificationService
{
    /**
     * Notify IT HOD(s) and Super Admins.
     */
    public function notifyItLeadership(Ticket $ticket, string $eventType, string $title, string $message): void
    {
        $recipients = User::where('role', 'super_admin')
            ->orWhere(function ($q) {
                $q->where('role', 'department_hod')
                  ->whereHas('department', function ($dq) {
                      $dq->where('code', 'IT');
                  });
            })
            ->where('is_active', true)
            ->get();

        $this->sendNotification($recipients, $ticket, $eventType, $title, $message);
    }

    /**
     * Notify complainant.
     */
    public function notifyComplainant(Ticket $ticket, string $eventType, string $title, string $message): void
    {
        if ($ticket->complainant && $ticket->complainant->is_active) {
            $this->sendNotification(collect([$ticket->complainant]), $ticket, $eventType, $title, $message);
        }
    }

    /**
     * Notify assigned technician.
     */
    public function notifyTechnician(Ticket $ticket, string $eventType, string $title, string $message): void
    {
        if ($ticket->assignedTechnician && $ticket->assignedTechnician->is_active) {
            $this->sendNotification(collect([$ticket->assignedTechnician]), $ticket, $eventType, $title, $message);
        }
    }

    /**
     * Send notification to a collection of users.
     */
    public function sendNotification(Collection $users, Ticket $ticket, string $eventType, string $title, string $message): void
    {
        $url = url("/admin/tickets/{$ticket->id}");

        foreach ($users as $user) {
            try {
                $user->notify(new TicketEventNotification(
                    ticket: $ticket,
                    eventType: $eventType,
                    title: $title,
                    messageContent: $message,
                    actionUrl: $url
                ));
            } catch (Throwable) {
                // Keep system running smoothly even if mail transport encounters offline network
            }
        }
    }
}
