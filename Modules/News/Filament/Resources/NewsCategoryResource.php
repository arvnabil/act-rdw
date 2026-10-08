<?php

namespace Modules\News\Filament\Resources;

use Modules\News\Filament\Resources\NewsCategoryResource\Pages;
use Modules\News\Filament\Resources\NewsCategoryResource\Schemas\NewsCategoryForm;
use Modules\News\Filament\Resources\NewsCategoryResource\Tables\NewsCategoryTable;
use Modules\News\Models\NewsCategory;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class NewsCategoryResource extends Resource
{
    protected static ?string $model = NewsCategory::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    protected static string | \UnitEnum | null $navigationGroup = 'News Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(NewsCategoryForm::schema());
    }

    public static function table(Table $table): Table
    {
        return NewsCategoryTable::table($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsCategories::route('/'),
            'create' => Pages\CreateNewsCategory::route('/create'),
            'edit' => Pages\EditNewsCategory::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasAnyPermission(['view_any_news_category', 'view_news_category', 'create_news_category', 'update_news_category', 'delete_news_category']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('create_news_category');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('update_news_category');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('delete_news_category');
    }
}
