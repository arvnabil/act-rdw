<?php

namespace App\Filament\Activioncms\Resources;

use App\Filament\Activioncms\Resources\NewsCategoryResource\Pages;
use App\Filament\Activioncms\Resources\NewsCategoryResource\Schemas\NewsCategoryForm;
use App\Filament\Activioncms\Resources\NewsCategoryResource\Tables\NewsCategoryTable;
use App\Models\NewsCategory;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class NewsCategoryResource extends Resource
{
    protected static ?string $model = NewsCategory::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    protected static string | \UnitEnum | null $navigationGroup = 'Site Management';

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
}
