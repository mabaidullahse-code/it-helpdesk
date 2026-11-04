<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentComplaintsWidget extends TableWidget
{
    protected static ?string $heading = 'Urgent & Recent Noticeable Complaints';

    protected static bool $isLazy = false;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $user = auth()->user();

        $query = Ticket::query()
            ->latest('created_at')
            ->limit(6);

        if ($user) {
            $query->accessibleBy($user);
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('tracking_id')
                    ->label('Tracking ID')
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('title')
                    ->label('Complaint Title')
                    ->weight('bold')
                    ->limit(45)
                    ->tooltip(fn (Ticket $record): string => $record->title),

                TextColumn::make('complainant.name')
                    ->label('Complainant')
                    ->placeholder('N/A'),

                TextColumn::make('department.name')
                    ->label('Department')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('priority')
                    ->label('Priority')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'critical' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        'low' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'submitted' => 'Submitted',
                        'under_review' => 'Under Review',
                        'in_progress' => 'In Progress',
                        'awaiting_confirmation' => 'Awaiting Confirmation',
                        'closed' => 'Closed',
                        'reopened' => 'Reopened',
                        'cancelled' => 'Cancelled',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'submitted' => 'info',
                        'under_review' => 'warning',
                        'in_progress' => 'primary',
                        'awaiting_confirmation' => 'purple',
                        'closed' => 'success',
                        'reopened' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Lodged')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Ticket $record): string => route('filament.admin.resources.tickets.view', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
