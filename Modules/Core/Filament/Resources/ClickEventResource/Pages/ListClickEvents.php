<?php

namespace Modules\Core\Filament\Resources\ClickEventResource\Pages;

use Modules\Core\Filament\Resources\ClickEventResource;
use Filament\Resources\Pages\ListRecords;

class ListClickEvents extends ListRecords
{
    protected static string $resource = ClickEventResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            ClickEventResource\Widgets\ClickEventsOverview::class,
            ClickEventResource\Widgets\EventsChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}
