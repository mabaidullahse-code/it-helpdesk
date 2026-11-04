<?php

namespace App\Filament\Resources\ComplaintCategories;

use App\Filament\Resources\ComplaintCategories\Pages\CreateComplaintCategory;
use App\Filament\Resources\ComplaintCategories\Pages\EditComplaintCategory;
use App\Filament\Resources\ComplaintCategories\Pages\ListComplaintCategories;
use App\Models\ComplaintCategory;
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

class ComplaintCategoryResource extends Resource
{
    protected static ?string $model = ComplaintCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static UnitEnum|string|null $navigationGroup = 'Service Desk Configuration';

    protected static ?int $navigationSort = 1;

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
                Section::make('Complaint Category Configuration')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Category Name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('slug')
                                ->label('URL Slug')
                                ->maxLength(255)
                                ->helperText('Leave empty to generate automatically from name.'),
                            Select::make('default_priority')
                                ->label('Default Priority')
                                ->options([
                                    'low' => 'Low',
                                    'medium' => 'Medium',
                                    'high' => 'High',
                                    'critical' => 'Critical',
                                ])
                                ->default('medium')
                                ->required(),
                            TextInput::make('sort_order')
                                ->label('Display Order')
                                ->numeric()
                                ->default(0),
                        ]),
                        Textarea::make('description')
                            ->label('Category Description & Guidelines')
                            ->rows(3),
                        Toggle::make('is_active')
                            ->label('Category Active')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Category Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('default_priority')
                    ->label('Default Priority')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'critical' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('subcategories_count')
                    ->counts('subcategories')
                    ->label('Subcategories')
                    ->sortable(),
                TextColumn::make('tickets_count')
                    ->counts('tickets')
                    ->label('Total Complaints')
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),
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
            'index' => ListComplaintCategories::route('/'),
            'create' => CreateComplaintCategory::route('/create'),
            'edit' => EditComplaintCategory::route('/{record}/edit'),
        ];
    }
}
