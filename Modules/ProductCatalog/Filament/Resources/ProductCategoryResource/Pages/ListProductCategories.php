<?php

namespace Modules\ProductCatalog\Filament\Resources\ProductCategoryResource\Pages;

use Modules\ProductCatalog\Filament\Resources\ProductCategoryResource;
use Filament\Resources\Pages\ListRecords;

class ListProductCategories extends ListRecords
{
    protected static string $resource = ProductCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
            \Filament\Actions\ImportAction::make('import_product_categories')
                ->importer(\Modules\ProductCatalog\Filament\Imports\ProductCategoryImporter::class)
                ->file(fn (\Filament\Forms\Components\FileUpload $file) => $file->acceptedFileTypes([
                    'text/csv', 'text/x-csv', 'application/csv', 'application/x-csv', 
                    'text/comma-separated-values', 'text/x-comma-separated-values', 
                    'text/plain', 'application/vnd.ms-excel', 'application/octet-stream',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                ])),
        ];
    }
}
