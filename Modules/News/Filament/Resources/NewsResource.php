<?php

namespace Modules\News\Filament\Resources;

use Modules\News\Filament\Resources\NewsResource\Pages;
use Modules\News\Filament\Resources\NewsResource\Schemas\NewsForm;
use Modules\News\Filament\Resources\NewsResource\Tables\NewsTable;
use Modules\News\Models\News;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class NewsResource extends Resource
{
    use \App\Traits\HasAuthorScope;

    protected static ?string $model = News::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-newspaper';

    protected static string | \UnitEnum | null $navigationGroup = 'News Management';

    public static function form(Schema $schema): Schema
    {
        return $schema->components(NewsForm::schema());
    }

    public static function table(Table $table): Table
    {
        return NewsTable::table($table);
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasAnyPermission([
                'view_any_news', 'view_news', 'create_news', 'update_news', 'delete_news', 'delete_any_news',
                'view_any_news_category', 'view_news_category', 'create_news_category', 'update_news_category', 'delete_news_category',
                'view_any_news_tag', 'view_news_tag', 'create_news_tag', 'update_news_tag', 'delete_news_tag',
            ]);
    }
}
