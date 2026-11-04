<?php

namespace App\Services;

use App\Models\SlaPolicy;
use App\Models\Ticket;
use Carbon\Carbon;

class SlaManagementService
{
    /**
     * Compute and set initial SLA deadlines for a ticket.
     */
    public function setInitialDeadlines(Ticket $ticket): void
    {
        $policy = SlaPolicy::getPolicyForPriority($ticket->priority);

        $responseHours = $policy?->response_time_hours ?? match ($ticket->priority) {
            'critical' => 1,
            'high' => 4,
            'medium' => 8,
            'low' => 24,
            default => 8,
        };

        $resolutionHours = $policy?->resolution_time_hours ?? match ($ticket->priority) {
            'critical' => 4,
            'high' => 12,
            'medium' => 24,
            'low' => 48,
            default => 24,
        };

        $submittedAt = $ticket->submitted_at ?? now();

        $ticket->sla_response_deadline = Carbon::parse($submittedAt)->addHours($responseHours);
        $ticket->sla_resolution_deadline = Carbon::parse($submittedAt)->addHours($resolutionHours);
    }

    /**
     * Pause SLA when a ticket is placed on hold or awaiting information.
     */
    public function pauseSla(Ticket $ticket): void
    {
        if (!$ticket->sla_paused_at) {
            $ticket->sla_paused_at = now();
        }
    }

    /**
     * Resume SLA when a ticket is unpaused.
     */
    public function resumeSla(Ticket $ticket): void
    {
        if ($ticket->sla_paused_at) {
            $pausedMinutes = Carbon::parse($ticket->sla_paused_at)->diffInMinutes(now());
            $ticket->sla_total_paused_minutes += (int) $pausedMinutes;

            // Shift deadlines forward by the paused duration
            if ($ticket->sla_response_deadline && !$ticket->sla_responded_at) {
                $ticket->sla_response_deadline = Carbon::parse($ticket->sla_response_deadline)->addMinutes($pausedMinutes);
            }

            if ($ticket->sla_resolution_deadline && !$ticket->completed_at) {
                $ticket->sla_resolution_deadline = Carbon::parse($ticket->sla_resolution_deadline)->addMinutes($pausedMinutes);
            }

            $ticket->sla_paused_at = null;
        }
    }

    /**
     * Mark SLA response as fulfilled.
     */
    public function markResponseFulfilled(Ticket $ticket): void
    {
        if (!$ticket->sla_responded_at) {
            $ticket->sla_responded_at = now();
            if ($ticket->sla_response_deadline && now()->gt($ticket->sla_response_deadline)) {
                $ticket->is_sla_response_breached = true;
            }
        }
    }

    /**
     * Check if a ticket has breached its resolution SLA.
     */
    public function checkBreach(Ticket $ticket): void
    {
        if ($ticket->isClosed()) {
            return;
        }

        if ($ticket->sla_resolution_deadline && now()->gt($ticket->sla_resolution_deadline)) {
            $ticket->is_sla_resolution_breached = true;
            $ticket->save();
        }
    }
}
