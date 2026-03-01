<?php

namespace Modules\ProductCatalog\Filament\Resources;

use Modules\ProductCatalog\Filament\Resources\ProductResource\Pages;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\ProductCatalog\Models\Product;
use Modules\ProductCatalog\Filament\Resources\ProductResource\Schemas\ProductForm;
use Modules\ProductCatalog\Filament\Resources\ProductResource\Tables\ProductTable;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cube';

    protected static string | \UnitEnum | null $navigationGroup = 'Product Catalog';

    protected static ?string $navigationLabel = 'Products';

    protected static ?string $modelLabel = 'Product';

    protected static ?string $pluralModelLabel = 'Products';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema(ProductForm::schema());
    }

    public static function table(Table $table): Table
    {
        return ProductTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
