<?php

namespace Modules\ServiceSolutions\Filament\Resources\ServiceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\ServiceSolutions\Filament\Resources\ServiceResource;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\ServiceImporter::class)
                ->modalDescription(fn () => new \Illuminate\Support\HtmlString('Download example CSV: <a href="#" wire:click.prevent="mountAction(\'downloadExample\')">Click here</a>')),
            Actions\Action::make('downloadExample')
                ->label('Download Example CSV')
                ->hidden()
                ->action(fn () => response()->download(public_path('examples/service-import.csv'))),
        ];
    }
}
