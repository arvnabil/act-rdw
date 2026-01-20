<?php

namespace App\Filament\Activioncms\Resources\SeoMetaResource\Pages;

use App\Filament\Activioncms\Resources\SeoMetaResource;
use Filament\Resources\Pages\ListRecords;

class ListSeoMetas extends ListRecords
{
    protected static string $resource = SeoMetaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
