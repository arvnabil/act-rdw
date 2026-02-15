<?php

namespace Modules\Core\Filament\Resources\WhatsAppAnalyticsResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\WhatsAppAnalyticsResource;

class ListWhatsAppAnalytics extends ListRecords
{
    protected static string $resource = WhatsAppAnalyticsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create button
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WhatsAppAnalyticsResource\Widgets\AnalyticsOverview::class,
        ];
    }
}
