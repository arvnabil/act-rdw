<?php

namespace Modules\News\Filament\Resources\NewsTagResource\Pages;

use Modules\News\Filament\Resources\NewsTagResource;
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
