<?php

namespace Modules\Core\Filament\Resources\ProductResource\Pages;

use Modules\Core\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\ProductImporter::class)
                ->label('Import Products')
                ->color('gray') 
                ->modalDescription(fn () => new \Illuminate\Support\HtmlString('Download example CSV: <a href="#" wire:click.prevent="mountAction(\'downloadExample\')">Click here</a>')),
            Actions\Action::make('downloadExample')
                ->label('Download Example CSV')
                ->hidden()
                ->action(fn () => response()->download(public_path('examples/product-import-example.csv'))),
        ];
    }
}
