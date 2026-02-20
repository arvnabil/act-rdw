<?php

namespace Modules\Core\Filament\Resources\ProductResource\Pages;

use Modules\Core\Filament\Resources\ProductResource;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->getRecord()->syncBrandToSolutions();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['specs'] = \App\Helpers\JsonHelper::toRepeater($data['specs'] ?? []);
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['specs'] = \App\Helpers\JsonHelper::fromRepeater($data['specs'] ?? []);
        return $data;
    }
}
