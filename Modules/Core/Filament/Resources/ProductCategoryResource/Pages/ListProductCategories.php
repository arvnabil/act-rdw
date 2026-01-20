<?php

namespace Modules\Core\Filament\Resources\ProductCategoryResource\Pages;

use Modules\Core\Filament\Resources\ProductCategoryResource;
use Filament\Resources\Pages\ListRecords;

class ListProductCategories extends ListRecords
{
    protected static string $resource = ProductCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
