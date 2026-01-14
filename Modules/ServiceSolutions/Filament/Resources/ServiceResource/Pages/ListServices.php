<?php

namespace Modules\ServiceSolutions\Filament\Resources\ServiceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\ServiceSolutions\Filament\Resources\ServiceResource;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
