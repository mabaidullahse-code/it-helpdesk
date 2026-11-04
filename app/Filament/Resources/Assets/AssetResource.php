<?php

namespace App\Filament\Resources\Assets;

use App\Filament\Resources\Assets\Pages\CreateAsset;
use App\Filament\Resources\Assets\Pages\EditAsset;
use App\Filament\Resources\Assets\Pages\ListAssets;
use App\Models\Asset;
use App\Models\Department;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static UnitEnum|string|null $navigationGroup = 'Complaints & Support Tickets';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isItStaff() || $user->isDepartmentHod());
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isItHod());
    }

    public static function canEdit(Model $record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isItHod());
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

        if ($user && ($user->isSuperAdmin() || $user->isItStaff())) {
            return parent::getEloquentQuery();
        }

        if ($user && $user->isDepartmentHod()) {
            return parent::getEloquentQuery()->where('department_id', $user->department_id);
        }

        return parent::getEloquentQuery()->whereRaw('1 = 0');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(['default' => 1, 'lg' => 3])
            ->components([
                // Left Column (Span 2): Hardware & Technical Specifications
                Group::make([
                    Section::make('Asset Hardware & Specifications')
                        ->description('Inventory tag, device classification, and manufacturer hardware details.')
                        ->icon(Heroicon::OutlinedComputerDesktop)
                        ->schema([
                            Grid::make(['default' => 1, 'sm' => 2, 'md' => 3])->schema([
                                TextInput::make('asset_tag')
                                    ->label('Asset Tag / Inventory ID')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('AST-2026-001'),
                                Select::make('asset_type')
                                    ->label('Asset Type')
                                    ->options([
                                        'Computer' => 'Desktop Computer',
                                        'Laptop' => 'Laptop / Notebook',
                                        'Printer' => 'Network Printer / Scanner',
                                        'Network Switch' => 'Network Switch',
                                        'Router' => 'Router / Gateway',
                                        'Access Point' => 'Wireless Access Point',
                                        'Server' => 'Server Machine',
                                        'UPS' => 'UPS / Backup Power',
                                        'Peripheral' => 'Peripheral / Monitor',
                                    ])
                                    ->required(),
                                TextInput::make('name')
                                    ->label('Device Name / Label')
                                    ->placeholder('e.g. HR Operations Desktop')
                                    ->maxLength(255),
                                TextInput::make('manufacturer')
                                    ->label('Manufacturer')
                                    ->placeholder('e.g. Dell, HP, Cisco, Lenovo'),
                                TextInput::make('model')
                                    ->label('Model')
                                    ->placeholder('e.g. OptiPlex 7090 MT'),
                                TextInput::make('serial_number')
                                    ->label('Serial Number')
                                    ->placeholder('e.g. DL7090-99214'),
                            ]),
                        ]),

                    Section::make('Network & Technical Configuration')
                        ->description('Network addressing and hardware technical specifications.')
                        ->icon(Heroicon::OutlinedSignal)
                        ->schema([
                            Grid::make(['default' => 1, 'sm' => 2])->schema([
                                TextInput::make('ip_address')
                                    ->label('Static IP Address (Optional)')
                                    ->placeholder('e.g. 192.168.10.45'),
                                TextInput::make('mac_address')
                                    ->label('MAC Address (Optional)')
                                    ->placeholder('e.g. 00:1A:2B:3C:4D:5E'),
                            ]),
                            Textarea::make('notes')
                                ->label('Technical Specifications & Configuration Notes')
                                ->placeholder('e.g. 16GB RAM, 512GB NVMe SSD, Core i7-11700, Windows 11 Pro...')
                                ->rows(3),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 2]),

                // Right Column (Span 1): Operational Custody, Status & Procurement Lifecycle
                Group::make([
                    Section::make('Operational Status & Custody')
                        ->description('Device operational state and departmental custody.')
                        ->icon(Heroicon::OutlinedBuildingOffice2)
                        ->schema([
                            Select::make('status')
                                ->label('Operational Status')
                                ->options([
                                    'in_service' => 'In Service',
                                    'under_maintenance' => 'Under Maintenance',
                                    'spare' => 'Spare / Available in Stock',
                                    'decommissioned' => 'Decommissioned / Written Off',
                                ])
                                ->default('in_service')
                                ->required(),
                            Select::make('department_id')
                                ->label('Assigned Department')
                                ->options(Department::where('is_active', true)->pluck('name', 'id'))
                                ->searchable(),
                            Select::make('assigned_user_id')
                                ->label('Assigned Custodian / User')
                                ->options(User::where('is_active', true)->pluck('name', 'id'))
                                ->searchable(),
                            TextInput::make('location')
                                ->label('Office / Room Location')
                                ->placeholder('e.g. Admin Block, Ground Floor, Room 104'),
                        ]),

                    Section::make('Procurement & Lifecycle')
                        ->description('Procurement date and active warranty.')
                        ->icon(Heroicon::OutlinedShieldCheck)
                        ->schema([
                            DatePicker::make('purchase_date')
                                ->label('Procurement Date'),
                            DatePicker::make('warranty_expiry')
                                ->label('Warranty Expiry Date'),
                        ]),
                ])->columnSpan(['default' => 1, 'lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset_tag')
                    ->label('Asset Tag')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Device Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('asset_type')
                    ->label('Type')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignedUser.name')
                    ->label('Assigned User')
                    ->placeholder('Unassigned')
                    ->searchable(),
                TextColumn::make('serial_number')
                    ->label('Serial No.')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'in_service' => 'success',
                        'under_maintenance' => 'warning',
                        'spare' => 'info',
                        'decommissioned' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('tickets_count')
                    ->counts('tickets')
                    ->label('Complaints')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('asset_type')
                    ->options([
                        'Computer' => 'Desktop Computer',
                        'Laptop' => 'Laptop',
                        'Printer' => 'Printer',
                        'Network Switch' => 'Network Switch',
                        'Server' => 'Server',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'in_service' => 'In Service',
                        'under_maintenance' => 'Under Maintenance',
                        'spare' => 'Spare',
                        'decommissioned' => 'Decommissioned',
                    ]),
                SelectFilter::make('department')
                    ->relationship('department', 'name'),
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
            'index' => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'edit' => EditAsset::route('/{record}/edit'),
        ];
    }
}
