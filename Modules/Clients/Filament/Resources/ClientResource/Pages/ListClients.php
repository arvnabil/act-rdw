<?php

namespace Modules\Clients\Filament\Resources\ClientResource\Pages;

use Modules\Clients\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ExportAction::make()
                ->exporter(\Modules\Clients\Filament\Exports\ClientExporter::class),
            Actions\ImportAction::make()
                ->importer(\Modules\Clients\Filament\Imports\ClientImporter::class)
                ->modalDescription(fn () => new \Illuminate\Support\HtmlString('Download example CSV: <a href="#" wire:click.prevent="mountAction(\'downloadExample\')">Click here</a>')),
            Actions\Action::make('downloadExample')
                ->label('Download Example CSV')
                ->hidden()
                ->action(fn () => response()->download(public_path('examples/client-import-example.csv'))),
        ];
    }
}
