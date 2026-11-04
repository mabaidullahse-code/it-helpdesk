<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use App\Services\ReportExportService;
use App\Services\TicketWorkflowService;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Assign / Reassign Technician (IT HOD / Super Admin)
            Action::make('assign_technician')
                ->label('Assign Technician')
                ->icon('heroicon-o-user-plus')
                ->color('primary')
                ->visible(fn () => auth()->user()?->isSuperAdmin() || auth()->user()?->isItHod())
                ->form([
                    Select::make('technician_id')
                        ->label('Select Technician')
                        ->options(function () {
                            return User::where('role', 'it_technician')
                                ->where('is_active', true)
                                ->withCount(['assignedTickets' => function ($q) {
                                    $q->whereNotIn('status', ['closed', 'cancelled']);
                                }])
                                ->get()
                                ->mapWithKeys(function ($tech) {
                                    return [$tech->id => "{$tech->name} ({$tech->designation}) — Active Load: {$tech->assigned_tickets_count}"];
                                });
                        })
                        ->required(),
                    Textarea::make('notes')
                        ->label('Assignment Instructions / Notes')
                        ->rows(2),
                ])
                ->action(function (array $data) {
                    /** @var Ticket $record */
                    $record = $this->getRecord();
                    $technician = User::findOrFail($data['technician_id']);
                    app(TicketWorkflowService::class)->assignTechnician($record, $technician, auth()->user(), $data['notes'] ?? null);
                    Notification::make()
                        ->title('Technician Assigned')
                        ->body("Ticket {$record->tracking_id} successfully assigned to {$technician->name}. Complainant has been notified via email.")
                        ->success()
                        ->send();
                }),

            // Start Work (Assigned Technician / IT HOD)
            Action::make('start_work')
                ->label('Start Work')
                ->icon('heroicon-o-play')
                ->color('info')
                ->visible(fn () => in_array($this->getRecord()->status, ['assigned', 'reopened'], true) &&
                    (auth()->user()?->isSuperAdmin() || auth()->user()?->isItHod() || auth()->id() === $this->getRecord()->assigned_technician_id))
                ->form([
                    Textarea::make('work_notes')
                        ->label('Initial Investigation Notes (Optional)')
                        ->rows(2),
                ])
                ->action(function (array $data) {
                    /** @var Ticket $record */
                    $record = $this->getRecord();
                    app(TicketWorkflowService::class)->startWork($record, auth()->user(), $data['work_notes'] ?? null);
                    Notification::make()
                        ->title('Work Started')
                        ->body("Ticket {$record->tracking_id} marked In Progress. Complainant notified via email.")
                        ->info()
                        ->send();
                }),

            // Mark Resolved / Awaiting Confirmation
            Action::make('mark_completed')
                ->label('Mark Resolved')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->getRecord()->status === 'in_progress' &&
                    (auth()->user()?->isSuperAdmin() || auth()->user()?->isItHod() || auth()->id() === $this->getRecord()->assigned_technician_id))
                ->form([
                    Textarea::make('resolution_summary')
                        ->label('Resolution Summary & Actions Taken')
                        ->required()
                        ->rows(3)
                        ->placeholder('Describe the hardware repair, replacement, network configuration, or actions taken...'),
                ])
                ->action(function (array $data) {
                    /** @var Ticket $record */
                    $record = $this->getRecord();
                    app(TicketWorkflowService::class)->markWorkCompleted($record, auth()->user(), $data['resolution_summary']);
                    Notification::make()
                        ->title('Work Marked Completed')
                        ->body("Ticket {$record->tracking_id} submitted for complainant confirmation. Email sent to ticket generator.")
                        ->success()
                        ->send();
                }),

            // Confirm Resolution (Complainant)
            Action::make('confirm_resolved')
                ->label('Yes, Issue Resolved')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn () => $this->getRecord()->isAwaitingConfirmation() &&
                    (auth()->id() === $this->getRecord()->complainant_id || auth()->user()?->isSuperAdmin()))
                ->form([
                    Radio::make('rating')
                        ->label('Service Satisfaction Rating')
                        ->options([
                            5 => '⭐⭐⭐⭐⭐ 5 Stars - Excellent Service',
                            4 => '⭐⭐⭐⭐ 4 Stars - Very Good',
                            3 => '⭐⭐⭐ 3 Stars - Satisfactory',
                            2 => '⭐⭐ 2 Stars - Needs Improvement',
                            1 => '⭐ 1 Star - Unsatisfactory',
                        ])
                        ->default(5)
                        ->required(),
                    Textarea::make('feedback_comments')
                        ->label('Optional Comments & Feedback')
                        ->rows(2),
                ])
                ->action(function (array $data) {
                    /** @var Ticket $record */
                    $record = $this->getRecord();
                    app(TicketWorkflowService::class)->confirmResolved($record, auth()->user(), (int) $data['rating'], $data['feedback_comments'] ?? null);
                    Notification::make()
                        ->title('Resolution Confirmed')
                        ->body("Complaint {$record->tracking_id} confirmed resolved and closed. Confirmation email sent.")
                        ->success()
                        ->send();
                }),

            // Reopen Complaint (Complainant)
            Action::make('reopen')
                ->label('No, Issue Persists')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->visible(fn () => $this->getRecord()->isAwaitingConfirmation() &&
                    (auth()->id() === $this->getRecord()->complainant_id || auth()->user()?->isSuperAdmin() || auth()->user()?->isItHod()))
                ->form([
                    Textarea::make('reopen_reason')
                        ->label('Reason Issue Still Persists')
                        ->required()
                        ->rows(3)
                        ->placeholder('Please explain why the issue is not resolved or what symptoms remain...'),
                ])
                ->action(function (array $data) {
                    /** @var Ticket $record */
                    $record = $this->getRecord();
                    app(TicketWorkflowService::class)->reopenTicket($record, auth()->user(), $data['reopen_reason']);
                    Notification::make()
                        ->title('Complaint Reopened')
                        ->body("Complaint {$record->tracking_id} has been reopened and returned to active queue. Email notifications sent.")
                        ->danger()
                        ->send();
                }),

            // Public Reply / Progress Update
            Action::make('add_comment')
                ->label('Reply / Add Update')
                ->icon('heroicon-o-chat-bubble-bottom-center-text')
                ->color('gray')
                ->form([
                    Textarea::make('comment')
                        ->label('Your Message / Reply')
                        ->required()
                        ->rows(3)
                        ->placeholder('Write your reply or information update here...'),
                ])
                ->action(function (array $data) {
                    /** @var Ticket $record */
                    $record = $this->getRecord();
                    app(TicketWorkflowService::class)->addComment(
                        ticket: $record,
                        user: auth()->user(),
                        comment: $data['comment'],
                        isInternal: false,
                        type: 'general'
                    );

                    Notification::make()
                        ->title('Reply Sent')
                        ->body('Your message has been posted. Notification email sent.')
                        ->success()
                        ->send();
                }),

            // Internal Technical Note (IT Personnel Only)
            Action::make('add_internal_note')
                ->label('Add Internal IT Note')
                ->icon('heroicon-o-lock-closed')
                ->color('warning')
                ->visible(fn () => auth()->user()?->isItStaff())
                ->form([
                    Textarea::make('comment')
                        ->label('Confidential Technical Troubleshooting Note')
                        ->helperText('Visible ONLY to IT staff members. Never disclosed to complainants.')
                        ->required()
                        ->rows(3)
                        ->placeholder('Diagnostic logs, router port tested, hardware replacement part numbers...'),
                ])
                ->action(function (array $data) {
                    /** @var Ticket $record */
                    $record = $this->getRecord();
                    app(TicketWorkflowService::class)->addComment(
                        ticket: $record,
                        user: auth()->user(),
                        comment: $data['comment'],
                        isInternal: true,
                        type: 'investigation'
                    );

                    Notification::make()
                        ->title('Internal Note Saved')
                        ->body('Confidential technical note added.')
                        ->success()
                        ->send();
                }),

            // Request Information from Complainant
            Action::make('request_info')
                ->label('Request Info')
                ->icon('heroicon-o-question-mark-circle')
                ->color('amber')
                ->visible(fn () => auth()->user()?->isItStaff() && in_array($this->getRecord()->status, ['assigned', 'in_progress'], true))
                ->form([
                    Textarea::make('request_message')
                        ->label('Information Requested from Complainant')
                        ->required()
                        ->rows(3)
                        ->placeholder('Specify what details or tests are needed from the user...'),
                ])
                ->action(function (array $data) {
                    /** @var Ticket $record */
                    $record = $this->getRecord();
                    app(TicketWorkflowService::class)->requestAdditionalInformation(
                        ticket: $record,
                        user: auth()->user(),
                        requestMessage: $data['request_message']
                    );

                    Notification::make()
                        ->title('Information Requested')
                        ->body('Notification email sent to complainant.')
                        ->warning()
                        ->send();
                }),

            // Print Individual Report
            Action::make('print_pdf')
                ->label('Print Report')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(function () {
                    /** @var Ticket $record */
                    $record = $this->getRecord();
                    return app(ReportExportService::class)->generateTicketPdf($record);
                }),
        ];
    }
}
