<?php

namespace Modules\News\Filament\Resources\NewsResource\Pages;

use Modules\News\Filament\Resources\NewsResource;
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
                ->importer(\Modules\News\Filament\Imports\NewsImporter::class),
        ];
    }
}
