<?php

namespace Modules\Projects\Filament\Resources\ProjectResource\Pages;

use Modules\Projects\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->importer(\Modules\Projects\Filament\Imports\ProjectImporter::class)
                ->modalDescription(fn () => new \Illuminate\Support\HtmlString('Download example CSV: <a href="#" wire:click.prevent="mountAction(\'downloadExample\')">Click here</a>'))
                ->file(fn (\Filament\Forms\Components\FileUpload $file) => $file->acceptedFileTypes([
                    'text/csv', 'text/x-csv', 'application/csv', 'application/x-csv', 
                    'text/comma-separated-values', 'text/x-comma-separated-values', 
                    'text/plain', 'application/vnd.ms-excel', 'application/octet-stream',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ])),
            Actions\Action::make('downloadExample')
                ->label('Download Example CSV')
                ->hidden()
                ->action(fn () => response()->download(public_path('examples/project-import-example.csv'))),
        ];
    }
}
