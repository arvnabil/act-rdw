<?php

namespace Modules\SEO\Filament\Resources\SeoWhitelistDomainResource\Pages;

use Modules\SEO\Filament\Resources\SeoWhitelistDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSeoWhitelistDomains extends ListRecords
{
    protected static string $resource = SeoWhitelistDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            \Filament\Actions\ImportAction::make('import')
                ->importer(\Modules\SEO\Filament\Imports\SeoWhitelistDomainImporter::class),
            \Filament\Actions\ExportAction::make('export')
                ->exporter(\Modules\SEO\Filament\Exports\SeoWhitelistDomainExporter::class),
        ];
    }
}
