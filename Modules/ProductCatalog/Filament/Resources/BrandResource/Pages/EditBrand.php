<?php

namespace Modules\ProductCatalog\Filament\Resources\BrandResource\Pages;

use Modules\ProductCatalog\Filament\Resources\BrandResource;
use Filament\Resources\Pages\EditRecord;

class EditBrand extends EditRecord
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
