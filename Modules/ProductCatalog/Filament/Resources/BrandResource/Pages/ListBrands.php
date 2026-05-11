<?php

namespace Modules\ProductCatalog\Filament\Resources\BrandResource\Pages;

use Modules\ProductCatalog\Filament\Resources\BrandResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBrands extends ListRecords
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ExportAction::make()
                ->exporter(\Modules\ProductCatalog\Filament\Exports\BrandExporter::class),
            \Filament\Actions\ImportAction::make('import_brands')
                ->importer(\Modules\ProductCatalog\Filament\Imports\BrandImporter::class)
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
                ->action(fn () => response()->download(public_path('examples/brand-import-example.csv'))),
        ];
    }
}
