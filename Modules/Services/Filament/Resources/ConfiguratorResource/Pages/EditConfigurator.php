<?php

namespace Modules\Services\Filament\Resources\ConfiguratorResource\Pages;

use Modules\Services\Filament\Resources\ConfiguratorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConfigurator extends EditRecord
{
    protected static string $resource = ConfiguratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
