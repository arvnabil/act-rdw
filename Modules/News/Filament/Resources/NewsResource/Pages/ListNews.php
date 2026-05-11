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
                ->importer(\Modules\News\Filament\Imports\NewsImporter::class)
                ->file(fn (\Filament\Forms\Components\FileUpload $file) => $file->acceptedFileTypes([
                    'text/csv', 'text/x-csv', 'application/csv', 'application/x-csv', 
                    'text/comma-separated-values', 'text/x-comma-separated-values', 
                    'text/plain', 'application/vnd.ms-excel', 'application/octet-stream',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ])),
        ];
    }
}
