<?php

namespace App\Filament\Activioncms\Resources\SeoWhitelistDomainResource\Pages;

use App\Filament\Activioncms\Resources\SeoWhitelistDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSeoWhitelistDomains extends ListRecords
{
    protected static string $resource = SeoWhitelistDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\SeoWhitelistDomainImporter::class),
            Actions\ExportAction::make()
                ->exporter(\App\Filament\Exports\SeoWhitelistDomainExporter::class),
        ];
    }
}
