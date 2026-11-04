<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\TicketComment;
use App\Models\TicketFeedback;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TicketWorkflowService
{
    public function __construct(
        protected TicketNumberService $ticketNumberService,
        protected SlaManagementService $slaService,
        protected NotificationService $notificationService,
        protected AuditLogService $auditLogService
    ) {
    }

    /**
     * Create a new complaint ticket.
     */
    public function createTicket(array $data, User $complainant): Ticket
    {
        return DB::transaction(function () use ($data, $complainant) {
            $trackingId = $this->ticketNumberService->generateTrackingId();

            $ticket = new Ticket();
            $ticket->tracking_id = $trackingId;
            $ticket->complainant_id = $complainant->id;
            $ticket->department_id = $data['department_id'] ?? $complainant->department_id;
            $ticket->category_id = $data['category_id'];
            $ticket->subcategory_id = $data['subcategory_id'] ?? null;
            $ticket->asset_id = $data['asset_id'] ?? null;
            $ticket->title = $data['title'];
            $ticket->description = $data['description'];
            $ticket->priority = $data['priority'] ?? 'medium';
            $ticket->status = 'submitted';
            $ticket->location = $data['location'] ?? null;
            $ticket->room_number = $data['room_number'] ?? null;
            $ticket->asset_tag_or_serial = $data['asset_tag_or_serial'] ?? null;
            $ticket->contact_number = $data['contact_number'] ?? $complainant->phone;
            $ticket->affected_users_count = $data['affected_users_count'] ?? 1;
            $ticket->impact_level = $data['impact_level'] ?? 'Individual';
            $ticket->preferred_visit_time = $data['preferred_visit_time'] ?? null;
            $ticket->additional_info = $data['additional_info'] ?? null;
            $ticket->submitted_at = now();

            // Set initial SLA targets
            $this->slaService->setInitialDeadlines($ticket);
            $ticket->save();

            // Record status timeline
            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'changed_by_user_id' => $complainant->id,
                'from_status' => null,
                'to_status' => 'submitted',
                'action_label' => 'Complaint Submitted',
                'comment' => 'Complaint lodged by ' . $complainant->name,
                'is_visible_to_complainant' => true,
            ]);

            // Audit log
            $this->auditLogService->log(
                action: 'created_complaint',
                auditable: $ticket,
                description: "New ticket {$trackingId} submitted by {$complainant->name}"
            );

            // Notifications
            $this->notificationService->notifyComplainant(
                $ticket,
                'submitted',
                'Complaint Submitted Successfully',
                "Your complaint {$trackingId} has been submitted. Our IT Hardware and Networking team will review it shortly."
            );

            $this->notificationService->notifyItLeadership(
                $ticket,
                'submitted',
                'New IT Complaint Submitted',
                "New complaint {$trackingId} submitted by {$complainant->name} ({$ticket->department?->name}): {$ticket->title}"
            );

            return $ticket;
        });
    }

    /**
     * Assign or reassign a technician.
     */
    public function assignTechnician(Ticket $ticket, User $technician, User $assigner, ?string $notes = null): Ticket
    {
        return DB::transaction(function () use ($ticket, $technician, $assigner, $notes) {
            $previousTechnicianId = $ticket->assigned_technician_id;
            $isReassignment = !empty($previousTechnicianId) && $previousTechnicianId !== $technician->id;

            $fromStatus = $ticket->status;
            $newStatus = in_array($ticket->status, ['in_progress', 'on_hold'], true) ? $ticket->status : 'assigned';

            $ticket->assigned_technician_id = $technician->id;
            $ticket->assigned_at = now();
            $ticket->status = $newStatus;

            // Fulfill SLA response
            $this->slaService->markResponseFulfilled($ticket);
            $ticket->save();

            // Assignment record
            TicketAssignment::create([
                'ticket_id' => $ticket->id,
                'assigned_to_user_id' => $technician->id,
                'assigned_by_user_id' => $assigner->id,
                'previous_technician_id' => $previousTechnicianId,
                'notes' => $notes,
                'assigned_at' => now(),
            ]);

            // Timeline record
            $actionLabel = $isReassignment ? 'Technician Reassigned' : 'Technician Assigned';
            $historyComment = $isReassignment
                ? "Reassigned to {$technician->name} ({$technician->designation}) by {$assigner->name}."
                : "Assigned to {$technician->name} ({$technician->designation}) by {$assigner->name}.";

            if ($notes) {
                $historyComment .= " Note: {$notes}";
            }

            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'changed_by_user_id' => $assigner->id,
                'from_status' => $fromStatus,
                'to_status' => $newStatus,
                'action_label' => $actionLabel,
                'comment' => $historyComment,
                'is_visible_to_complainant' => true,
            ]);

            // Audit log
            $this->auditLogService->log(
                action: $isReassignment ? 'reassigned_technician' : 'assigned_technician',
                auditable: $ticket,
                description: "Ticket {$ticket->tracking_id} assigned to {$technician->name} by {$assigner->name}"
            );

            // Notifications
            $this->notificationService->notifyComplainant(
                $ticket,
                'assigned',
                'Technician Assigned',
                "Your complaint {$ticket->tracking_id} has been assigned to {$technician->name}, {$technician->designation}. You will receive further updates as work progresses."
            );

            $this->notificationService->notifyTechnician(
                $ticket,
                'assigned',
                'Ticket Assigned To You',
                "You have been assigned complaint {$ticket->tracking_id}: {$ticket->title}. Assigned by {$assigner->name}."
            );

            return $ticket;
        });
    }

    /**
     * Mark work started by technician.
     */
    public function startWork(Ticket $ticket, User $user, ?string $workNotes = null): Ticket
    {
        return DB::transaction(function () use ($ticket, $user, $workNotes) {
            $fromStatus = $ticket->status;
            $ticket->status = 'in_progress';
            $ticket->work_started_at = $ticket->work_started_at ?? now();
            $ticket->save();

            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'changed_by_user_id' => $user->id,
                'from_status' => $fromStatus,
                'to_status' => 'in_progress',
                'action_label' => 'Work Started',
                'comment' => $workNotes ?: "Work started by {$user->name}",
                'is_visible_to_complainant' => true,
            ]);

            if ($workNotes) {
                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'comment' => $workNotes,
                    'is_internal' => false,
                    'type' => 'investigation',
                ]);
            }

            $this->auditLogService->log(
                action: 'work_started',
                auditable: $ticket,
                description: "Work started on ticket {$ticket->tracking_id} by {$user->name}"
            );

            $this->notificationService->notifyComplainant(
                $ticket,
                'work_started',
                'Work Started on Your Complaint',
                "Work has started on your complaint {$ticket->tracking_id}. The assigned technician is investigating the issue."
            );

            return $ticket;
        });
    }

    /**
     * Mark work as completed and request complainant confirmation.
     */
    public function markWorkCompleted(Ticket $ticket, User $user, string $resolutionSummary): Ticket
    {
        return DB::transaction(function () use ($ticket, $user, $resolutionSummary) {
            $fromStatus = $ticket->status;
            $ticket->status = 'awaiting_confirmation';
            $ticket->completed_at = now();
            $ticket->resolution_summary = $resolutionSummary;
            $ticket->save();

            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'changed_by_user_id' => $user->id,
                'from_status' => $fromStatus,
                'to_status' => 'awaiting_confirmation',
                'action_label' => 'Work Completed / Awaiting Confirmation',
                'comment' => "Resolution summary: {$resolutionSummary}",
                'is_visible_to_complainant' => true,
            ]);

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => "Resolution Summary: {$resolutionSummary}",
                'is_internal' => false,
                'type' => 'resolution_summary',
            ]);

            $this->auditLogService->log(
                action: 'work_completed',
                auditable: $ticket,
                description: "Work marked completed on ticket {$ticket->tracking_id} by {$user->name}"
            );

            $this->notificationService->notifyComplainant(
                $ticket,
                'work_completed',
                'Work Completed - Confirmation Required',
                "Your complaint {$ticket->tracking_id} has been marked as resolved by the IT support team. Please log in to confirm whether your issue has been resolved."
            );

            return $ticket;
        });
    }

    /**
     * Complainant confirms issue is resolved.
     */
    public function confirmResolved(Ticket $ticket, User $complainant, ?int $rating = null, ?string $feedbackComments = null): Ticket
    {
        return DB::transaction(function () use ($ticket, $complainant, $rating, $feedbackComments) {
            $fromStatus = $ticket->status;
            $ticket->status = 'closed';
            $ticket->confirmed_resolved_at = now();
            $ticket->closed_at = now();
            $ticket->save();

            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'changed_by_user_id' => $complainant->id,
                'from_status' => $fromStatus,
                'to_status' => 'closed',
                'action_label' => 'Resolution Confirmed',
                'comment' => 'Complainant confirmed that the issue is successfully resolved. Ticket closed.',
                'is_visible_to_complainant' => true,
            ]);

            if ($rating !== null) {
                TicketFeedback::updateOrCreate(
                    ['ticket_id' => $ticket->id],
                    [
                        'user_id' => $complainant->id,
                        'rating' => max(1, min(5, $rating)),
                        'comments' => $feedbackComments,
                    ]
                );
            }

            $this->auditLogService->log(
                action: 'confirmed_resolved',
                auditable: $ticket,
                description: "Ticket {$ticket->tracking_id} confirmed resolved and closed by {$complainant->name}"
            );

            $this->notificationService->notifyComplainant(
                $ticket,
                'confirmed_resolved',
                'Complaint Confirmed Resolved & Closed',
                "Thank you. Your complaint {$ticket->tracking_id} has been confirmed resolved and officially closed."
            );

            $this->notificationService->notifyTechnician(
                $ticket,
                'confirmed_resolved',
                'Complaint Confirmed Resolved and Closed',
                "Complainant {$complainant->name} confirmed that complaint {$ticket->tracking_id} is resolved."
            );

            $this->notificationService->notifyItLeadership(
                $ticket,
                'confirmed_resolved',
                'Complaint Closed - Resolution Confirmed',
                "Complaint {$ticket->tracking_id} was confirmed resolved by {$complainant->name} and closed."
            );

            return $ticket;
        });
    }

    /**
     * Complainant reports issue persists and reopens the complaint.
     */
    public function reopenTicket(Ticket $ticket, User $complainant, string $reopenReason): Ticket
    {
        return DB::transaction(function () use ($ticket, $complainant, $reopenReason) {
            $fromStatus = $ticket->status;
            $ticket->status = 'reopened';
            $ticket->reopened_at = now();
            $ticket->reopen_reason = $reopenReason;
            $ticket->save();

            // Resume SLA
            $this->slaService->resumeSla($ticket);

            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'changed_by_user_id' => $complainant->id,
                'from_status' => $fromStatus,
                'to_status' => 'reopened',
                'action_label' => 'Complaint Reopened',
                'comment' => "Issue not resolved. Reason: {$reopenReason}",
                'is_visible_to_complainant' => true,
            ]);

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $complainant->id,
                'comment' => "Issue Still Persists / Reopened: {$reopenReason}",
                'is_internal' => false,
                'type' => 'general',
            ]);

            $this->auditLogService->log(
                action: 'reopened_ticket',
                auditable: $ticket,
                description: "Ticket {$ticket->tracking_id} reopened by {$complainant->name}: {$reopenReason}"
            );

            $this->notificationService->notifyComplainant(
                $ticket,
                'reopened',
                'Complaint Reopened',
                "Your complaint {$ticket->tracking_id} has been reopened because the issue persists: {$reopenReason}. Our IT team has been notified to resume work."
            );

            $this->notificationService->notifyTechnician(
                $ticket,
                'reopened',
                'Complaint Reopened - Action Required',
                "Complaint {$ticket->tracking_id} has been reopened by {$complainant->name}. Reason: {$reopenReason}"
            );

            $this->notificationService->notifyItLeadership(
                $ticket,
                'reopened',
                'Complaint Reopened',
                "Complaint {$ticket->tracking_id} was reopened by {$complainant->name}. Reason: {$reopenReason}"
            );

            return $ticket;
        });
    }

    /**
     * Request additional information from complainant.
     */
    public function requestAdditionalInformation(Ticket $ticket, User $user, string $requestMessage): Ticket
    {
        return DB::transaction(function () use ($ticket, $user, $requestMessage) {
            $fromStatus = $ticket->status;
            $ticket->status = 'awaiting_information';
            $this->slaService->pauseSla($ticket);
            $ticket->save();

            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'changed_by_user_id' => $user->id,
                'from_status' => $fromStatus,
                'to_status' => 'awaiting_information',
                'action_label' => 'Information Requested',
                'comment' => $requestMessage,
                'is_visible_to_complainant' => true,
            ]);

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => $requestMessage,
                'is_internal' => false,
                'type' => 'info_request',
            ]);

            $this->auditLogService->log(
                action: 'info_requested',
                auditable: $ticket,
                description: "Additional info requested for {$ticket->tracking_id} by {$user->name}"
            );

            $this->notificationService->notifyComplainant(
                $ticket,
                'info_requested',
                'Additional Information Required',
                "The IT support team has requested more details regarding complaint {$ticket->tracking_id}: {$requestMessage}"
            );

            return $ticket;
        });
    }

    /**
     * Add comment to ticket (public or internal).
     */
    public function addComment(Ticket $ticket, User $user, string $comment, bool $isInternal = false, string $type = 'general'): TicketComment
    {
        return DB::transaction(function () use ($ticket, $user, $comment, $isInternal, $type) {
            $ticketComment = TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'comment' => $comment,
                'is_internal' => $isInternal,
                'type' => $type,
            ]);

            // If complainant replies while awaiting information, resume work/assigned
            if ($ticket->status === 'awaiting_information' && $user->id === $ticket->complainant_id) {
                $fromStatus = $ticket->status;
                $newStatus = $ticket->assigned_technician_id ? 'in_progress' : 'submitted';
                $ticket->status = $newStatus;
                $this->slaService->resumeSla($ticket);
                $ticket->save();

                TicketStatusHistory::create([
                    'ticket_id' => $ticket->id,
                    'changed_by_user_id' => $user->id,
                    'from_status' => $fromStatus,
                    'to_status' => $newStatus,
                    'action_label' => 'Information Provided by Complainant',
                    'comment' => 'Complainant responded with requested details.',
                    'is_visible_to_complainant' => true,
                ]);

                $this->notificationService->notifyTechnician(
                    $ticket,
                    'info_provided',
                    'Complainant Responded',
                    "The complainant has provided the requested details for {$ticket->tracking_id}: {$comment}"
                );
            } elseif (!$isInternal && $user->id !== $ticket->complainant_id) {
                // Public technician update
                $this->notificationService->notifyComplainant(
                    $ticket,
                    'progress_update',
                    'New Progress Update on Complaint',
                    "A progress update was added to your complaint {$ticket->tracking_id}: {$comment}"
                );
            }

            return $ticketComment;
        });
    }
}
