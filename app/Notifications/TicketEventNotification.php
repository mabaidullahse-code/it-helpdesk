<?php

namespace App\Notifications;

use App\Models\EmailNotificationLog;
use App\Models\SystemSetting;
use App\Models\Ticket;
use App\Models\User;
use App\Services\EmailSettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Throwable;

class TicketEventNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public string $eventType,
        public string $title,
        public string $messageContent,
        public ?string $actionUrl = null
    ) {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Send email if user has an email address
        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Apply runtime dynamic mail config
        app(EmailSettingsService::class)->applyRuntimeConfiguration();

        $trackingId = $this->ticket->tracking_id;
        $subject = "[$trackingId] {$this->title}";

        // Record log entry
        EmailNotificationLog::create([
            'ticket_id' => $this->ticket->id,
            'recipient_email' => $notifiable->email,
            'recipient_name' => $notifiable->name ?? 'User',
            'subject' => $subject,
            'event_type' => $this->eventType,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $statusLabels = [
            'submitted' => 'Submitted / In Queue',
            'assigned' => 'Assigned to Technician',
            'in_progress' => 'Work in Progress',
            'awaiting_information' => 'Awaiting Information from Complainant',
            'awaiting_confirmation' => 'Work Completed (Pending Your Confirmation)',
            'closed' => 'Closed / Confirmed Resolved',
            'reopened' => 'Reopened',
            'cancelled' => 'Cancelled',
        ];

        $statusLabel = $statusLabels[$this->ticket->status] ?? ucwords(str_replace('_', ' ', $this->ticket->status));

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting("Dear {$notifiable->name},")
            ->line($this->messageContent)
            ->line("**Ticket Tracking ID:** {$this->ticket->tracking_id}")
            ->line("**Complaint Title:** {$this->ticket->title}")
            ->line("**Current Status:** {$statusLabel}")
            ->line("**Department:** " . ($this->ticket->department?->name ?? 'N/A'))
            ->line("**Category:** " . ($this->ticket->category?->name ?? 'N/A'))
            ->line("**Priority:** " . ucfirst($this->ticket->priority));

        if ($this->ticket->assignedTechnician) {
            $mailMessage->line("**Assigned Technician:** {$this->ticket->assignedTechnician->name} ({$this->ticket->assignedTechnician->designation})");
        }

        if ($this->ticket->resolution_summary && in_array($this->eventType, ['work_completed', 'confirmed_resolved', 'closed'], true)) {
            $mailMessage->line("**Resolution Summary:** {$this->ticket->resolution_summary}");
        }

        if ($this->actionUrl) {
            $mailMessage->action('Open Ticket in Portal', $this->actionUrl);
        }

        if ($this->eventType === 'work_completed') {
            $mailMessage->line('**Important:** Please log in to confirm that the issue is resolved on your workstation, or reopen the ticket if it still persists.');
        }

        $mailMessage->line('Thank you for contacting the UHS IT Hardware & Networking Helpdesk.');

        return $mailMessage;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'tracking_id' => $this->ticket->tracking_id,
            'event_type' => $this->eventType,
            'title' => $this->title,
            'message' => $this->messageContent,
            'status' => $this->ticket->status,
            'action_url' => $this->actionUrl,
        ];
    }
}
