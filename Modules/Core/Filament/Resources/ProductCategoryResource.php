<?php

namespace Modules\Core\Filament\Resources;

use Modules\Core\Filament\Resources\ProductCategoryResource\Pages;
use Modules\Core\Filament\Resources\ProductCategoryResource\Schemas\ProductCategoryForm;
use Modules\Core\Filament\Resources\ProductCategoryResource\Tables\ProductCategoryTable;
use Modules\Core\Models\ProductCategory;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProductCategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Core';

    protected static ?string $navigationLabel = 'Device Categories';

    protected static ?string $modelLabel = 'Product Category';

    protected static ?string $pluralModelLabel = 'Product Categories';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components(ProductCategoryForm::schema());
    }

    public static function table(Table $table): Table
    {
        return ProductCategoryTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductCategories::route('/'),
            'create' => Pages\CreateProductCategory::route('/create'),
            'edit' => Pages\EditProductCategory::route('/{record}/edit'),
        ];
    }
}
