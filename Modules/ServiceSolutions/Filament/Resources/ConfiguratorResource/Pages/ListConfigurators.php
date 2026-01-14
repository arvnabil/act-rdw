<?php

namespace Modules\ServiceSolutions\Filament\Resources\ConfiguratorResource\Pages;

use Modules\ServiceSolutions\Filament\Resources\ConfiguratorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConfigurators extends ListRecords
{
    protected static string $resource = ConfiguratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
