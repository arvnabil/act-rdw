<?php

namespace App\Filament\Activioncms\Resources\NewsResource\Pages;

use App\Filament\Activioncms\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNews extends ListRecords
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\NewsImporter::class),
        ];
    }
}
