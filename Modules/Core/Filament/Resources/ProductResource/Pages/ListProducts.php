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
            Actions\ImportAction::make('importLinks')
                ->importer(\App\Filament\Imports\ProductLinkImporter::class)
                ->label('Import Links Only')
                ->color('info')
                ->modalDescription(fn () => new \Illuminate\Support\HtmlString('Update Ecommerce Links via CSV. Column: <code>slug</code> (required), <code>link_accommerce</code>, <code>whatsapp_note</code>.')),
            Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\ProductImporter::class)
                ->label('Import Products')
                ->color('gray') 
                ->modalDescription(fn () => new \Illuminate\Support\HtmlString('Download example CSV: <a href="#" wire:click.prevent="mountAction(\'downloadExample\')">Click here</a>')),
            Actions\Action::make('downloadExample')
                ->label('Download Example CSV')
                ->hidden()
                ->action(fn () => response()->download(public_path('examples/product-import-example.csv'))),
            
            Actions\ExportAction::make('export')
                ->exporter(\App\Filament\Exports\ProductExporter::class)
                ->label('Export All')
                ->color('success'),
                
            Actions\ExportAction::make('exportLinks')
                ->exporter(\App\Filament\Exports\ProductLinkExporter::class)
                ->label('Export Links Only')
                ->color('warning')
                ->icon('heroicon-o-link'),
        ];
    }
}
