<?php

namespace App\Filament\Resources\KnowledgeArticles;

use App\Filament\Resources\KnowledgeArticles\Pages\CreateKnowledgeArticle;
use App\Filament\Resources\KnowledgeArticles\Pages\EditKnowledgeArticle;
use App\Filament\Resources\KnowledgeArticles\Pages\ListKnowledgeArticles;
use App\Models\ComplaintCategory;
use App\Models\KnowledgeArticle;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class KnowledgeArticleResource extends Resource
{
    protected static ?string $model = KnowledgeArticle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static UnitEnum|string|null $navigationGroup = 'Service Desk Configuration';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return true; // All authenticated employees can search and read articles
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isItStaff());
    }

    public static function canEdit(Model $record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isItStaff());
    }

    public static function canDelete(Model $record): bool
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->isItHod());
    }

    public static function getEloquentQuery(): Builder
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();

        if ($user && ($user->isSuperAdmin() || $user->isItStaff())) {
            return parent::getEloquentQuery();
        }

        // Non-IT staff only see published articles
        return parent::getEloquentQuery()->where('is_published', true);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Article Content & Classification')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('title')
                                ->label('Article Title')
                                ->required()
                                ->maxLength(255),
                            Select::make('category_id')
                                ->label('Related Category')
                                ->options(ComplaintCategory::where('is_active', true)->pluck('name', 'id'))
                                ->searchable(),
                            TextInput::make('keywords')
                                ->label('Search Keywords / Tags')
                                ->placeholder('e.g. printer, wifi, password, email'),
                            Toggle::make('is_published')
                                ->label('Published (Visible to all staff)')
                                ->default(true),
                        ]),
                        Textarea::make('summary')
                            ->label('Brief Summary / Quick Answer')
                            ->rows(2),
                        Textarea::make('content')
                            ->label('Full Troubleshooting Guide & Steps')
                            ->rows(8)
                            ->required(),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                TextColumn::make('author.name')
                    ->label('Author')
                    ->searchable(),
                TextColumn::make('views_count')
                    ->label('Views')
                    ->sortable(),
                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y')
                    ->sortable(),
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
            'index' => ListKnowledgeArticles::route('/'),
            'create' => CreateKnowledgeArticle::route('/create'),
            'edit' => EditKnowledgeArticle::route('/{record}/edit'),
        ];
    }
}
