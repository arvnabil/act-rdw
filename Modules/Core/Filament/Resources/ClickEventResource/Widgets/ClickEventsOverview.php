<?php

namespace Modules\Core\Filament\Resources\ClickEventResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Core\Models\AnalyticsClickEvent;

class ClickEventsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $humanTotal = AnalyticsClickEvent::where('is_bot', false)->count();
        $converted = AnalyticsClickEvent::where('is_converted', true)->count();
        $revenue = AnalyticsClickEvent::where('is_converted', true)->sum('deal_value');
        $conversionRate = $humanTotal > 0 ? ($converted / $humanTotal) * 100 : 0;

        return [
            Stat::make('Total Events', number_format($humanTotal))
                ->description('Human interactions (Bots excluded)')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Conversion Rate', number_format($conversionRate, 1) . '%')
                ->description('Converted leads from total clicks')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Total Revenue', 'IDR ' . number_format($revenue, 0, ',', '.'))
                ->description('Sum of deal values from conversions')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
                
            Stat::make('WA & Calls', AnalyticsClickEvent::whereIn('event_type', ['whatsapp', 'call'])->count())
                ->description('Direct contact interactions')
                ->descriptionIcon('heroicon-m-phone')
                ->color('info'),
        ];
    }
}
