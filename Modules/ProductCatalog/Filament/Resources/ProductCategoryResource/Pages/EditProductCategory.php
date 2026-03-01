<?php

namespace Modules\ProductCatalog\Filament\Resources\ProductCategoryResource\Pages;

use Modules\ProductCatalog\Filament\Resources\ProductCategoryResource;
use Filament\Resources\Pages\EditRecord;

class EditProductCategory extends EditRecord
{
    protected static string $resource = ProductCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
