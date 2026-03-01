<?php

namespace Modules\ProductCatalog\Filament\Resources\ProductCategoryResource\Pages;

use Modules\ProductCatalog\Filament\Resources\ProductCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductCategory extends CreateRecord
{
    protected static string $resource = ProductCategoryResource::class;
}
