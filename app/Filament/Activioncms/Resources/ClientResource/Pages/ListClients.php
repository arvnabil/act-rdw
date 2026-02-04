<?php

namespace App\Filament\Activioncms\Resources\ClientResource\Pages;

use App\Filament\Activioncms\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\ClientImporter::class),
        ];
    }
}
