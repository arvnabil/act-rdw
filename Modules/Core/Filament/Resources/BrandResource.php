<?php

namespace Modules\Core\Filament\Resources;

use Modules\Core\Filament\Resources\BrandResource\Pages;
use Modules\Core\Filament\Resources\BrandResource\Schemas\BrandForm;
use Modules\Core\Models\Brand;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\BrandResource\Tables\BrandTable;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    protected static string | \UnitEnum | null $navigationGroup = 'Core';

    protected static ?string $navigationLabel = 'Brands';

    protected static ?string $modelLabel = 'Brand';

    protected static ?string $pluralModelLabel = 'Brands';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components(BrandForm::schema());
    }

    public static function table(Table $table): Table
    {
        return BrandTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
