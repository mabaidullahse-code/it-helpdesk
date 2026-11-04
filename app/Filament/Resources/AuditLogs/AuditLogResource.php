<?php

namespace App\Filament\Resources\AuditLogs;

use App\Filament\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Models\AuditLog;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static UnitEnum|string|null $navigationGroup = 'Administration & Organization';

    protected static ?int $navigationSort = 3;

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }

    public static function canCreate(): bool
    {
        return false; // Append-only system log
    }

    public static function canEdit(Model $record): bool
    {
        return false; // Immutable audit log
    }

    public static function canDelete(Model $record): bool
    {
        return false; // Immutable audit log
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('d M Y, h:i:s A')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Responsible User')
                    ->placeholder('System')
                    ->searchable(),
                TextColumn::make('action')
                    ->label('Action Performed')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'created_user', 'assigned_technician', 'work_started' => 'info',
                        'work_completed', 'confirmed_resolved' => 'success',
                        'reopened_ticket' => 'danger',
                        default => 'secondary',
                    })
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Description & Context')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->options([
                        'created_complaint' => 'Created Complaint',
                        'assigned_technician' => 'Assigned Technician',
                        'reassigned_technician' => 'Reassigned Technician',
                        'work_started' => 'Work Started',
                        'work_completed' => 'Work Completed',
                        'confirmed_resolved' => 'Confirmed Resolved',
                        'reopened_ticket' => 'Reopened Ticket',
                        'info_requested' => 'Info Requested',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
        ];
    }
}
