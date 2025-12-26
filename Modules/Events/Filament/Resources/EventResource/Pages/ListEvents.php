<?php

namespace Modules\Events\Filament\Resources\EventResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Events\Filament\Resources\EventResource;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
