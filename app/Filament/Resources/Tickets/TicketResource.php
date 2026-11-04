<?php

namespace App\Filament\Resources\Tickets;

use App\Filament\Resources\Tickets\Pages\CreateTicket;
use App\Filament\Resources\Tickets\Pages\EditTicket;
use App\Filament\Resources\Tickets\Pages\ListTickets;
use App\Filament\Resources\Tickets\Pages\ViewTicket;
use App\Models\Asset;
use App\Models\ComplaintCategory;
use App\Models\ComplaintSubcategory;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\User;
use App\Services\ReportExportService;
use App\Services\TicketWorkflowService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static UnitEnum|string|null $navigationGroup = 'Complaints & Support Tickets';

    protected static ?string $navigationLabel = 'All Complaints';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && (bool) $user->is_active;
    }

    public static function canEdit(Model $record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (!$user) return false;
        return $user->isSuperAdmin() || $user->isItHod();
    }

    public static function canDelete(Model $record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }

    public static function getEloquentQuery(): Builder
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        $query = parent::getEloquentQuery()
            ->with(['department', 'category', 'complainant', 'assignedTechnician']);

        if ($user) {
            $query->accessibleBy($user);
        }

        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (!$user) return null;

        $count = static::getEloquentQuery()->open()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Incident Overview & Status')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(['default' => 2, 'sm' => 2, 'md' => 4, 'lg' => 4])->schema([
                            \Filament\Infolists\Components\TextEntry::make('tracking_id')
                                ->label('Tracking ID')
                                ->badge()
                                ->color('primary')
                                ->copyable()
                                ->weight('bold'),
                            \Filament\Infolists\Components\TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                                ->color(fn ($record) => $record->status_badge_color),
                            \Filament\Infolists\Components\TextEntry::make('priority')
                                ->label('Priority')
                                ->badge()
                                ->color(fn ($state) => match ($state) {
                                    'critical' => 'danger',
                                    'high' => 'warning',
                                    'medium' => 'info',
                                    default => 'secondary',
                                }),
                            \Filament\Infolists\Components\TextEntry::make('department.name')
                                ->label('Department')
                                ->badge()
                                ->color('gray'),
                        ]),
                        \Filament\Infolists\Components\TextEntry::make('title')
                            ->label('Complaint Title')
                            ->weight('bold')
                            ->columnSpanFull(),
                        \Filament\Infolists\Components\TextEntry::make('description')
                            ->label('Detailed Issue Description')
                            ->columnSpanFull(),
                    ]),

                Grid::make(['default' => 1, 'md' => 2])
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Technical Assignment & SLA Status')
                            ->schema([
                                Grid::make(2)->schema([
                                    \Filament\Infolists\Components\TextEntry::make('assignedTechnician.name')
                                        ->label('Assigned Technician')
                                        ->placeholder('Queue pending assignment')
                                        ->weight('bold'),
                                    \Filament\Infolists\Components\TextEntry::make('submitted_at')
                                        ->label('Submitted At')
                                        ->dateTime('d M Y, h:i A'),
                                    \Filament\Infolists\Components\TextEntry::make('work_started_at')
                                        ->label('Work Started At')
                                        ->dateTime('d M Y, h:i A')
                                        ->placeholder('Not started yet'),
                                    \Filament\Infolists\Components\TextEntry::make('sla_resolution_deadline')
                                        ->label('Resolution SLA Deadline')
                                        ->dateTime('d M Y, h:i A')
                                        ->color(fn ($record) => ($record->isOpen() && $record->sla_resolution_deadline && now()->gt($record->sla_resolution_deadline)) ? 'danger' : 'gray'),
                                ]),
                                \Filament\Infolists\Components\TextEntry::make('resolution_summary')
                                    ->label('Resolution Summary & Actions Taken')
                                    ->visible(fn ($record) => !empty($record->resolution_summary))
                                    ->weight('bold'),
                            ]),

                        Section::make('Location & Equipment Details')
                            ->schema([
                                Grid::make(2)->schema([
                                    \Filament\Infolists\Components\TextEntry::make('category.name')
                                        ->label('Service Category'),
                                    \Filament\Infolists\Components\TextEntry::make('subcategory.name')
                                        ->label('Subcategory')
                                        ->placeholder('N/A'),
                                    \Filament\Infolists\Components\TextEntry::make('location')
                                        ->label('Building / Block')
                                        ->placeholder('N/A'),
                                    \Filament\Infolists\Components\TextEntry::make('room_number')
                                        ->label('Room / Office')
                                        ->placeholder('N/A'),
                                    \Filament\Infolists\Components\TextEntry::make('asset.name')
                                        ->label('Linked Hardware Asset')
                                        ->placeholder('None specified'),
                                    \Filament\Infolists\Components\TextEntry::make('impact_level')
                                        ->label('Impact Scope')
                                        ->placeholder('Individual Workstation'),
                                ]),
                            ]),
                    ]),

                Section::make('Activity Timeline & Communication History')
                    ->columnSpanFull()
                    ->schema([
                        \Filament\Infolists\Components\ViewEntry::make('timeline')
                            ->view('filament.tickets.timeline-and-comments'),
                    ]),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = auth()->user();
        $isSuperAdmin = $authUser && $authUser->isSuperAdmin();

        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                // Left Column (Span 2): Primary Complaint Details & Hardware/Location
                Group::make([
                    Section::make('Complaint Details')
                        ->description('Specify the issue summary, category, urgency, and comprehensive incident description.')
                        ->icon(Heroicon::OutlinedChatBubbleBottomCenterText)
                        ->schema([
                            TextInput::make('title')
                                ->label('Complaint Title / Short Summary')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('e.g. Internet unreachable in Room 104 or Printer showing offline'),
                            Grid::make(['default' => 1, 'sm' => 3])->schema([
                                Select::make('category_id')
                                    ->label('Complaint Category')
                                    ->options(ComplaintCategory::where('is_active', true)->pluck('name', 'id'))
                                    ->reactive()
                                    ->required(),
                                Select::make('subcategory_id')
                                    ->label('Subcategory (Optional)')
                                    ->options(function (callable $get) {
                                        $catId = $get('category_id');
                                        if (!$catId) return [];
                                        return ComplaintSubcategory::where('category_id', $catId)->where('is_active', true)->pluck('name', 'id');
                                    })
                                    ->nullable(),
                                Select::make('priority')
                                    ->label('Priority Level')
                                    ->options([
                                        'low' => 'Low — Minor routine assistance',
                                        'medium' => 'Medium — Single user disruption',
                                        'high' => 'High — Departmental disruption',
                                        'critical' => 'Critical — Organization-wide outage',
                                    ])
                                    ->default('medium')
                                    ->disabled(!$authUser?->isItStaff())
                                    ->dehydrated(true)
                                    ->required(),
                            ]),
                            Textarea::make('description')
                                ->label('Detailed Description of the Issue')
                                ->required()
                                ->rows(5)
                                ->placeholder('Describe exactly what happened, error messages seen, and what actions were tried...'),
                        ]),

                    Section::make('Hardware & Location Information')
                        ->description('Physical office location, affected inventory asset, and impact scope for IT technicians.')
                        ->icon(Heroicon::OutlinedMapPin)
                        ->schema([
                            Grid::make(['default' => 1, 'sm' => 2])->schema([
                                TextInput::make('location')
                                    ->label('Building / Wing / Floor')
                                    ->placeholder('e.g. Admin Block, 1st Floor'),
                                TextInput::make('room_number')
                                    ->label('Room / Office / Workstation No.')
                                    ->placeholder('e.g. Room 104, Desk 3'),
                                Select::make('asset_id')
                                    ->label('Affected Asset (Equipment Register)')
                                    ->options(function () use ($authUser) {
                                        $q = Asset::where('status', 'in_service');
                                        if ($authUser && !$authUser->isItStaff()) {
                                            $q->where('department_id', $authUser->department_id);
                                        }
                                        return $q->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->nullable(),
                                TextInput::make('asset_tag_or_serial')
                                    ->label('Asset Tag / Serial / Computer Name')
                                    ->placeholder('e.g. AST-2026-001 or Dell-PC-12'),
                                TextInput::make('affected_users_count')
                                    ->label('Estimated Affected Users')
                                    ->numeric()
                                    ->default(1),
                                Select::make('impact_level')
                                    ->label('Impact on Work')
                                    ->options([
                                        'Individual' => 'Individual Workstation Affected',
                                        'Office' => 'Office / Team Affected',
                                        'Department' => 'Entire Department Disrupted',
                                        'Organization' => 'Organization-Wide Failure',
                                    ])
                                    ->default('Individual'),
                                TextInput::make('preferred_visit_time')
                                    ->label('Preferred Visit Time (Optional)')
                                    ->placeholder('e.g. Between 11:00 AM and 1:00 PM')
                                    ->columnSpan(['default' => 1, 'sm' => 2]),
                            ]),
                            Textarea::make('additional_info')
                                ->label('Additional Information / Remarks')
                                ->rows(2)
                                ->placeholder('Any access prerequisites, gate pass requirements, or alternative contacts...'),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 2]),

                // Right Column (Span 1): Complainant Profile & SLA Reference
                Group::make([
                    Section::make('Complainant Information')
                        ->description('Identity and contact details of the officer lodging the complaint.')
                        ->icon(Heroicon::OutlinedUserCircle)
                        ->schema([
                            Select::make('complainant_id')
                                ->label('Complainant')
                                ->options(User::where('is_active', true)->pluck('name', 'id'))
                                ->default($authUser?->id)
                                ->disabled(!$isSuperAdmin)
                                ->dehydrated(true)
                                ->searchable()
                                ->required(),
                            Select::make('department_id')
                                ->label('Complainant Department')
                                ->options(Department::where('is_active', true)->pluck('name', 'id'))
                                ->default($authUser?->department_id)
                                ->disabled(!$isSuperAdmin)
                                ->dehydrated(true)
                                ->searchable()
                                ->required(),
                            TextInput::make('contact_number')
                                ->label('Preferred Contact Number')
                                ->default($authUser?->phone)
                                ->tel()
                                ->placeholder('e.g. 0300-1112233 / Ext. 104'),
                        ]),

                    Section::make('Helpdesk Guidelines & SLA')
                        ->description('Expected response windows based on priority level.')
                        ->icon(Heroicon::OutlinedInformationCircle)
                        ->schema([
                            View::make('filament.tickets.sla-guidelines'),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tracking_id')
                    ->label('Tracking ID')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->limit(35)
                    ->weight('bold'),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priority')
                    ->label('Priority')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'critical' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                    ->color(fn ($record) => $record->status_badge_color)
                    ->sortable(),
                TextColumn::make('assignedTechnician.name')
                    ->label('Assigned Technician')
                    ->placeholder('Unassigned')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('complainant.name')
                    ->label('Complainant')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
                TextColumn::make('sla_resolution_deadline')
                    ->label('SLA Target')
                    ->dateTime('d M, h:i A')
                    ->color(fn ($record) => ($record->isOpen() && $record->sla_resolution_deadline && now()->gt($record->sla_resolution_deadline)) ? 'danger' : 'gray')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'under_review' => 'Under Review',
                        'assigned' => 'Assigned',
                        'in_progress' => 'In Progress',
                        'awaiting_information' => 'Awaiting Information',
                        'on_hold' => 'On Hold',
                        'awaiting_confirmation' => 'Awaiting Confirmation',
                        'reopened' => 'Reopened',
                        'escalated' => 'Escalated',
                        'closed' => 'Closed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('priority')
                    ->options([
                        'critical' => 'Critical',
                        'high' => 'High',
                        'medium' => 'Medium',
                        'low' => 'Low',
                    ]),
                SelectFilter::make('department')
                    ->relationship('department', 'name'),
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                SelectFilter::make('assigned_technician_id')
                    ->label('Technician')
                    ->relationship('assignedTechnician', 'name'),
                Filter::make('overdue')
                    ->label('Overdue / SLA Breached')
                    ->query(fn ($q) => $q->overdue()),
                Filter::make('unassigned')
                    ->label('Unassigned Complaints')
                    ->query(fn ($q) => $q->unassigned()),
            ])
            ->recordActions([
                ViewAction::make(),

                // Assign or Reassign Technician (IT HOD / Super Admin)
                Action::make('assign')
                    ->label(fn ($record) => $record->assigned_technician_id ? 'Reassign' : 'Assign')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->visible(fn ($record) => (auth()->user()?->isSuperAdmin() || auth()->user()?->isItHod()) && !$record->isClosed())
                    ->form([
                        Select::make('technician_id')
                            ->label('Select IT Technician')
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
                    ->action(function (Ticket $record, array $data) {
                        $technician = User::findOrFail($data['technician_id']);
                        app(TicketWorkflowService::class)->assignTechnician($record, $technician, auth()->user(), $data['notes'] ?? null);
                        Notification::make()
                            ->title('Technician Assigned')
                            ->body("Ticket {$record->tracking_id} successfully assigned to {$technician->name}.")
                            ->success()
                            ->send();
                    }),

                // Start Work (Assigned Technician / IT HOD)
                Action::make('start_work')
                    ->label('Start Work')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->visible(fn ($record) => in_array($record->status, ['assigned', 'reopened'], true) &&
                        (auth()->user()?->isSuperAdmin() || auth()->user()?->isItHod() || auth()->id() === $record->assigned_technician_id))
                    ->form([
                        Textarea::make('work_notes')
                            ->label('Initial Investigation Notes (Optional)')
                            ->rows(2),
                    ])
                    ->action(function (Ticket $record, array $data) {
                        app(TicketWorkflowService::class)->startWork($record, auth()->user(), $data['work_notes'] ?? null);
                        Notification::make()
                            ->title('Work Started')
                            ->body("Ticket {$record->tracking_id} marked In Progress.")
                            ->info()
                            ->send();
                    }),

                // Mark Completed / Awaiting Confirmation
                Action::make('mark_completed')
                    ->label('Mark Resolved')
                    ->icon('heroicon-o-check-circle')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'in_progress' &&
                        (auth()->user()?->isSuperAdmin() || auth()->user()?->isItHod() || auth()->id() === $record->assigned_technician_id))
                    ->form([
                        Textarea::make('resolution_summary')
                            ->label('Resolution Summary & Actions Taken')
                            ->required()
                            ->rows(3)
                            ->placeholder('Describe the diagnosis, hardware replacement, network fixes, or actions taken to resolve the issue...'),
                    ])
                    ->action(function (Ticket $record, array $data) {
                        app(TicketWorkflowService::class)->markWorkCompleted($record, auth()->user(), $data['resolution_summary']);
                        Notification::make()
                            ->title('Work Marked Completed')
                            ->body("Ticket {$record->tracking_id} submitted for complainant confirmation.")
                            ->success()
                            ->send();
                    }),

                // Confirm Resolution (Complainant)
                Action::make('confirm_resolved')
                    ->label('Yes, Issue Resolved')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => $record->isAwaitingConfirmation() &&
                        (auth()->id() === $record->complainant_id || auth()->user()?->isSuperAdmin()))
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
                    ->action(function (Ticket $record, array $data) {
                        app(TicketWorkflowService::class)->confirmResolved($record, auth()->user(), (int) $data['rating'], $data['feedback_comments'] ?? null);
                        Notification::make()
                            ->title('Resolution Confirmed')
                            ->body("Complaint {$record->tracking_id} confirmed resolved and closed.")
                            ->success()
                            ->send();
                    }),

                // Reopen Complaint (Complainant)
                Action::make('reopen')
                    ->label('No, Issue Persists')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->visible(fn ($record) => $record->isAwaitingConfirmation() &&
                        (auth()->id() === $record->complainant_id || auth()->user()?->isSuperAdmin() || auth()->user()?->isItHod()))
                    ->form([
                        Textarea::make('reopen_reason')
                            ->label('Reason Issue Still Persists')
                            ->required()
                            ->rows(3)
                            ->placeholder('Please explain why the issue is not resolved or what symptoms remain...'),
                    ])
                    ->action(function (Ticket $record, array $data) {
                        app(TicketWorkflowService::class)->reopenTicket($record, auth()->user(), $data['reopen_reason']);
                        Notification::make()
                            ->title('Complaint Reopened')
                            ->body("Complaint {$record->tracking_id} has been reopened and returned to active queue.")
                            ->danger()
                            ->send();
                    }),

                // Print PDF report
                Action::make('print_pdf')
                    ->label('Print Report')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->action(function (Ticket $record) {
                        return app(ReportExportService::class)->generateTicketPdf($record);
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => static::canDeleteAny()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTickets::route('/'),
            'create' => CreateTicket::route('/create'),
            'view' => ViewTicket::route('/{record}'),
            'edit' => EditTicket::route('/{record}/edit'),
        ];
    }
}
