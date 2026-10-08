<?php

namespace Modules\ProductCatalog\Filament\Resources;

use Modules\ProductCatalog\Filament\Resources\ProductCategoryResource\Pages;
use Modules\ProductCatalog\Filament\Resources\ProductCategoryResource\Schemas\ProductCategoryForm;
use Modules\ProductCatalog\Filament\Resources\ProductCategoryResource\Tables\ProductCategoryTable;
use Modules\ProductCatalog\Models\ProductCategory;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProductCategoryResource extends Resource
{
    protected static ?string $model = ProductCategory::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Product Catalog';

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

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasAnyPermission(['view_any_product', 'view_product', 'create_product', 'update_product', 'delete_product']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('create_product');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('update_product');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('delete_product');
    }
}
