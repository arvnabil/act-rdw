<?php

namespace App\Filament\Activioncms\Resources;

use App\Filament\Activioncms\Resources\NewsTagResource\Pages;
use App\Filament\Activioncms\Resources\NewsTagResource\Schemas\NewsTagForm;
use App\Filament\Activioncms\Resources\NewsTagResource\Tables\NewsTagTable;
use App\Models\NewsTag;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsTagResource extends Resource
{
    protected static ?string $model = NewsTag::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-hashtag';

    protected static string | \UnitEnum | null $navigationGroup = 'Site Management';

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
