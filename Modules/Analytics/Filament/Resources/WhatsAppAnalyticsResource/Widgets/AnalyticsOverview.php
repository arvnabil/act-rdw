<?php

namespace Modules\Analytics\Filament\Resources\WhatsAppAnalyticsResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;


class AnalyticsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Clicks', \Modules\Analytics\Models\AnalyticsClickEvent::where('event_type', 'whatsapp')->count())
                ->description('All time WhatsApp clicks')
                ->descriptionIcon('heroicon-m-cursor-arrow-rays')
                ->color('primary'),
                
            Stat::make('Today', \Modules\Analytics\Models\AnalyticsClickEvent::where('event_type', 'whatsapp')->whereDate('created_at', today())->count())
                ->description('Clicks today')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),

            Stat::make('Top Source', \Modules\Analytics\Models\AnalyticsClickEvent::where('event_type', 'whatsapp')
                    ->select('utm_source')
                    ->whereNotNull('utm_source')
                    ->groupBy('utm_source')
                    ->orderByRaw('COUNT(*) DESC')
                    ->limit(1)
                    ->value('utm_source') ?? 'Direct'
                )
                ->description('Most frequent UTM Source')
                ->color('warning'),
        ];
    }
}
