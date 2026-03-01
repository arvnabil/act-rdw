<?php

namespace Modules\ProductCatalog\Filament\Resources\ProductResource\Pages;

use Modules\ProductCatalog\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        $this->getRecord()->syncBrandToSolutions();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['specs'] = \App\Helpers\JsonHelper::fromRepeater($data['specs'] ?? []);
        return $data;
    }
}
