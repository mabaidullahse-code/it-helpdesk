<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Department;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static UnitEnum|string|null $navigationGroup = 'Administration & Organization';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isDepartmentHod());
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isDepartmentHod());
    }

    public static function canEdit(Model $record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        if (!$user) return false;
        if ($user->isSuperAdmin()) return true;
        if ($user->isDepartmentHod()) {
            if ($record->id === $user->id) {
                return true;
            }
            return $record->department_id === $user->department_id 
                && $record->role !== 'super_admin'
                && $record->role !== 'department_hod';
        }
        return false;
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

        if ($user && $user->isSuperAdmin()) {
            return parent::getEloquentQuery();
        }

        if ($user && $user->isDepartmentHod()) {
            return parent::getEloquentQuery()
                ->where('department_id', $user->department_id)
                ->where(function ($q) use ($user) {
                    $q->where('id', $user->id)
                        ->orWhere(function ($sub) {
                            $sub->where('role', '!=', 'super_admin')
                                ->where('role', '!=', 'department_hod');
                        });
                });
        }

        return parent::getEloquentQuery()->whereRaw('1 = 0');
    }

    public static function form(Schema $schema): Schema
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = auth()->user();
        $isSuperAdmin = $authUser && $authUser->isSuperAdmin();

        return $schema
            ->components([
                Section::make('Employee Personal & Institutional Details')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Full Name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('designation')
                                ->label('Designation / Job Title')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('username')
                                ->label('Account Username (Unique)')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(60),
                            TextInput::make('employee_id')
                                ->label('Staff Number / Employee ID (Optional)')
                                ->nullable()
                                ->unique(ignoreRecord: true)
                                ->maxLength(50),
                            TextInput::make('email')
                                ->label('Email Address (Optional)')
                                ->email()
                                ->nullable()
                                ->unique(ignoreRecord: true),
                            TextInput::make('phone')
                                ->label('Contact Phone / Extension (Optional)')
                                ->tel()
                                ->nullable(),
                            Select::make('department_id')
                                ->label('Department')
                                ->options(Department::where('is_active', true)->pluck('name', 'id'))
                                ->default($authUser?->department_id)
                                ->disabled(!$isSuperAdmin)
                                ->dehydrated(true)
                                ->required(),
                            Select::make('role')
                                ->label('Role')
                                ->options(function (string $operation = 'edit') use ($isSuperAdmin, $authUser) {
                                    if ($isSuperAdmin) {
                                        return [
                                            'super_admin' => 'Super Administrator',
                                            'department_hod' => 'Department HOD',
                                            'it_technician' => 'IT Support Technician',
                                            'employee' => 'Department Employee / Complainant',
                                        ];
                                    }

                                    if ($operation === 'create') {
                                        if ($authUser && $authUser->isItHod()) {
                                            return [
                                                'it_technician' => 'IT Support Technician',
                                                'employee' => 'Department Employee',
                                            ];
                                        }

                                        return [
                                            'employee' => 'Department Employee',
                                        ];
                                    }

                                    if ($authUser && $authUser->isItHod()) {
                                        return [
                                            'department_hod' => 'Department HOD',
                                            'it_technician' => 'IT Support Technician',
                                            'employee' => 'Department Employee',
                                        ];
                                    }

                                    return [
                                        'department_hod' => 'Department HOD',
                                        'employee' => 'Department Employee',
                                    ];
                                })
                                ->default('employee')
                                ->disabled(function (?User $record) use ($isSuperAdmin, $authUser): bool {
                                    if ($isSuperAdmin) {
                                        return false;
                                    }

                                    // HOD can never change their own role or any HOD's role
                                    return $record?->id === $authUser?->id || $record?->role === 'department_hod';
                                })
                                ->helperText(function (?User $record) use ($isSuperAdmin, $authUser): ?string {
                                    if (!$isSuperAdmin && ($record?->id === $authUser?->id || $record?->role === 'department_hod')) {
                                        return 'The Department HOD role is permanent and cannot be modified.';
                                    }

                                    return null;
                                })
                                ->dehydrated(true)
                                ->required(),
                            TextInput::make('password')
                                ->label('Set Account Password')
                                ->password()
                                ->revealable()
                                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                                ->dehydrated(fn ($state) => filled($state))
                                ->required(fn (string $operation): bool => $operation === 'create')
                                ->helperText('Leave blank when editing to preserve the existing password.'),
                            TextInput::make('plain_password')
                                ->label('Plain Password')
                                ->password()
                                ->revealable()
                                ->nullable()
                                ->helperText('Stored plain text password for administrative reference and recovery.'),
                            Toggle::make('is_active')
                                ->label('Account Active')
                                ->default(true)
                                ->disabled(fn (?User $record) => !$isSuperAdmin || $record?->id === $authUser?->id)
                                ->dehydrated(true)
                                ->helperText(function (?User $record) use ($isSuperAdmin, $authUser): ?string {
                                    if (!$isSuperAdmin) {
                                        return 'Only Super Administrators can activate or deactivate accounts.';
                                    }
                                    if ($record?->id === $authUser?->id) {
                                        return 'You cannot deactivate your own account.';
                                    }
                                    return null;
                                }),
                        ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Employee Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('username')
                    ->label('Username')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('designation')
                    ->label('Designation')
                    ->searchable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'super_admin' => 'Super Admin',
                        'department_hod' => 'Department HOD',
                        'it_technician' => 'IT Technician',
                        'employee' => 'Employee',
                        default => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'super_admin' => 'danger',
                        'department_hod' => 'warning',
                        'it_technician' => 'info',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->placeholder('N/A')
                    ->searchable(),
                TextColumn::make('plain_password')
                    ->label('Plain Password')
                    ->copyable()
                    ->placeholder('—')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->placeholder('N/A'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->relationship('department', 'name'),
                SelectFilter::make('role')
                    ->options([
                        'super_admin' => 'Super Administrator',
                        'department_hod' => 'Department HOD',
                        'it_technician' => 'IT Support Technician',
                        'employee' => 'Department Employee',
                    ]),
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
