<?php

namespace App\Filament\Activioncms\Resources;

use App\Filament\Activioncms\Resources\NewsResource\Pages;
use App\Filament\Activioncms\Resources\NewsResource\Schemas\NewsForm;
use App\Filament\Activioncms\Resources\NewsResource\Tables\NewsTable;
use App\Models\News;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-newspaper';

    protected static string | \UnitEnum | null $navigationGroup = 'Site Management';

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
}
