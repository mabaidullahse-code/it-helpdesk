<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class TicketNumberService
{
    /**
     * Generate a unique, monotonic, server-side tracking ID.
     * Format: ITHN-YYYY-000001
     */
    public function generateTrackingId(): string
    {
        return DB::transaction(function () {
            $year = now()->format('Y');
            $prefix = "ITHN-{$year}-";

            // Find the latest ticket created this year with the matching prefix
            $latestTicket = Ticket::where('tracking_id', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $sequence = 1;
            if ($latestTicket && preg_match('/ITHN-\d{4}-(\d+)/', $latestTicket->tracking_id, $matches)) {
                $sequence = (int) $matches[1] + 1;
            }

            $formattedSequence = str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
            $trackingId = "{$prefix}{$formattedSequence}";

            // Ensure uniqueness in edge cases
            while (Ticket::where('tracking_id', $trackingId)->exists()) {
                $sequence++;
                $formattedSequence = str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
                $trackingId = "{$prefix}{$formattedSequence}";
            }

            return $trackingId;
        });
    }
}
