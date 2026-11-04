<?php

namespace App\Filament\Resources\Departments;

use App\Filament\Resources\Departments\Pages\CreateDepartment;
use App\Filament\Resources\Departments\Pages\EditDepartment;
use App\Filament\Resources\Departments\Pages\ListDepartments;
use App\Models\Department;
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

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static UnitEnum|string|null $navigationGroup = 'Administration & Organization';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isDepartmentHod());
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user && $user->isSuperAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Department Profile')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Department Name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('code')
                                ->label('Department Code (Unique)')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(30)
                                ->extraInputAttributes(['style' => 'text-transform: uppercase']),
                            Select::make('hod_user_id')
                                ->label('Head of Department (HOD)')
                                ->relationship('hod', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                            TextInput::make('location')
                                ->label('Building / Office Location')
                                ->maxLength(255),
                            TextInput::make('phone')
                                ->label('Contact Phone / Extension')
                                ->tel(),
                            TextInput::make('email')
                                ->label('Department Email')
                                ->email(),
                        ]),
                        Textarea::make('description')
                            ->label('Description & Responsibilities')
                            ->rows(3),
                        Toggle::make('is_active')
                            ->label('Department Active')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Department Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hod.name')
                    ->label('Department HOD')
                    ->placeholder('Unassigned')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Employees')
                    ->sortable(),
                TextColumn::make('tickets_count')
                    ->counts('tickets')
                    ->label('Total Complaints')
                    ->sortable(),
                TextColumn::make('location')
                    ->label('Location')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                //
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
            'index' => ListDepartments::route('/'),
            'create' => CreateDepartment::route('/create'),
            'edit' => EditDepartment::route('/{record}/edit'),
        ];
    }
}
