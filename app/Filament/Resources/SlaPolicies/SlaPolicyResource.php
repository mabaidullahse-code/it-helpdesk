<?php

namespace App\Filament\Resources\SlaPolicies;

use App\Filament\Resources\SlaPolicies\Pages\CreateSlaPolicy;
use App\Filament\Resources\SlaPolicies\Pages\EditSlaPolicy;
use App\Filament\Resources\SlaPolicies\Pages\ListSlaPolicies;
use App\Models\SlaPolicy;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class SlaPolicyResource extends Resource
{
    protected static ?string $model = SlaPolicy::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static UnitEnum|string|null $navigationGroup = 'Service Desk Configuration';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isItHod());
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Service Level Agreement (SLA) Targets')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Policy Name')
                                ->required()
                                ->maxLength(255),
                            Select::make('priority')
                                ->label('Applies to Priority')
                                ->options([
                                    'critical' => 'Critical Priority',
                                    'high' => 'High Priority',
                                    'medium' => 'Medium Priority',
                                    'low' => 'Low Priority',
                                ])
                                ->unique(ignoreRecord: true)
                                ->required(),
                            TextInput::make('response_time_hours')
                                ->label('Target First Response / Assignment (Hours)')
                                ->numeric()
                                ->required()
                                ->minValue(1),
                            TextInput::make('resolution_time_hours')
                                ->label('Target Resolution Time (Hours)')
                                ->numeric()
                                ->required()
                                ->minValue(1),
                            TextInput::make('escalation_threshold_hours')
                                ->label('Escalation Warning Threshold (Hours)')
                                ->numeric()
                                ->nullable(),
                            TextInput::make('reminder_interval_hours')
                                ->label('Technician Reminder Interval (Hours)')
                                ->numeric()
                                ->nullable(),
                        ]),
                        Textarea::make('description')
                            ->label('Policy Description & Applicability')
                            ->rows(2),
                        Toggle::make('is_active')
                            ->label('Policy Active')
                            ->default(true),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Policy Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
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
                TextColumn::make('response_time_hours')
                    ->label('Target Response')
                    ->formatStateUsing(fn ($state) => "{$state} hrs")
                    ->sortable(),
                TextColumn::make('resolution_time_hours')
                    ->label('Target Resolution')
                    ->formatStateUsing(fn ($state) => "{$state} hrs")
                    ->sortable(),
                TextColumn::make('escalation_threshold_hours')
                    ->label('Escalation Threshold')
                    ->formatStateUsing(fn ($state) => $state ? "{$state} hrs" : 'N/A'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn (Model $record): bool => static::canEdit($record)),
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
            'index' => ListSlaPolicies::route('/'),
            'create' => CreateSlaPolicy::route('/create'),
            'edit' => EditSlaPolicy::route('/{record}/edit'),
        ];
    }
}
