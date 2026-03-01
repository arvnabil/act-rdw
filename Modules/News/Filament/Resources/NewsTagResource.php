<?php

namespace Modules\News\Filament\Resources;

use Modules\News\Filament\Resources\NewsTagResource\Pages;
use Modules\News\Filament\Resources\NewsTagResource\Schemas\NewsTagForm;
use Modules\News\Filament\Resources\NewsTagResource\Tables\NewsTagTable;
use Modules\News\Models\NewsTag;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsTagResource extends Resource
{
    protected static ?string $model = NewsTag::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-hashtag';

    protected static string | \UnitEnum | null $navigationGroup = 'News Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(NewsTagForm::schema());
    }

    public static function table(Table $table): Table
    {
        return NewsTagTable::table($table);
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
            'index' => Pages\ListNewsTags::route('/'),
            'create' => Pages\CreateNewsTags::route('/create'),
            'edit' => Pages\EditNewsTags::route('/{record}/edit'),
        ];
    }
}
