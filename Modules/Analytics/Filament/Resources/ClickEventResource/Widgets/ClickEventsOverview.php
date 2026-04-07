<?php

namespace Modules\Analytics\Filament\Resources\ClickEventResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Analytics\Models\AnalyticsClickEvent;

class ClickEventsOverview extends BaseWidget
{
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // Default: show today's data
        $query = AnalyticsClickEvent::whereDate('created_at', now());

        $humanTotal = (clone $query)->where('is_bot', false)->count();
        $converted = (clone $query)->where('is_converted', true)->count();
        $botTotal = (clone $query)->where('is_bot', true)->count();
        $waCalls = (clone $query)->whereIn('event_type', ['whatsapp', 'call'])->count();

        $conversionRate = $humanTotal > 0 ? ($converted / $humanTotal) * 100 : 0;

        return [
            Stat::make('Total Events', number_format($humanTotal))
                ->description('Human clicks today')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Conversion Rate', number_format($conversionRate, 1) . '%')
                ->description('Converted leads from total clicks')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Total Bot Detection', number_format($botTotal))
                ->description('Automated traffic identified today')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('warning'),

            Stat::make('WA & Calls', number_format($waCalls))
                ->description('WhatsApp and Call clicks today')
                ->descriptionIcon('heroicon-m-phone')
                ->color('info'),
        ];
    }
}
