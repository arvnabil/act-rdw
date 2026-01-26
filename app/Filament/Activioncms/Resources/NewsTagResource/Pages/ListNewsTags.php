<?php

namespace App\Filament\Activioncms\Resources\NewsTagResource\Pages;

use App\Filament\Activioncms\Resources\NewsTagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNewsTags extends ListRecords
{
    protected static string $resource = NewsTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
