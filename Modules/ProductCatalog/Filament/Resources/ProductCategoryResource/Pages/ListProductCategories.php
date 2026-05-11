<?php

namespace Modules\ProductCatalog\Filament\Resources\ProductCategoryResource\Pages;

use Modules\ProductCatalog\Filament\Resources\ProductCategoryResource;
use Filament\Resources\Pages\ListRecords;

class ListProductCategories extends ListRecords
{
    protected static string $resource = ProductCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
            \Filament\Actions\ImportAction::make('import_product_categories')
                ->importer(\Modules\ProductCatalog\Filament\Imports\ProductCategoryImporter::class),
        ];
    }
}
