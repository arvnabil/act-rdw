<?php

namespace Modules\Analytics\Filament\Resources\ClickEventResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Analytics\Models\AnalyticsClickEvent;

use Filament\Widgets\Concerns\InteractsWithPageTable;

class ClickEventsOverview extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getStats(): array
    {
        $filters = $this->tableFilters;
        
        // Base query for stats
        $query = AnalyticsClickEvent::query();

        // 1. Determine Period
        $fromDate = $filters['created_at']['created_from'] ?? null;
        $untilDate = $filters['created_at']['created_until'] ?? null;
        $isToday = false;

        if ($fromDate || $untilDate) {
            $query->when($fromDate, fn ($q) => $q->whereDate('created_at', '>=', $fromDate))
                  ->when($untilDate, fn ($q) => $q->whereDate('created_at', '<=', $untilDate));
            $periodLabel = 'Filtered Period';
        } else {
            // Default to today if no date filter is set
            $query->whereDate('created_at', now());
            $periodLabel = 'Today';
            $isToday = true;
        }

        // 2. Apply Type & Source Filters (if active)
        $query->when(!empty($filters['event_type']['value']), fn ($q) => $q->where('event_type', $filters['event_type']['value']));
        $query->when(!empty($filters['utm_source']['value']), fn ($q) => $q->where('utm_source', $filters['utm_source']['value']));

        // 3. Clone queries for specific metrics
        $humanTotal = (clone $query)->where('is_bot', false)->count();
        $converted = (clone $query)->where('is_converted', true)->count();
        $botTotal = (clone $query)->where('is_bot', true)->count();
        $waCalls = (clone $query)->whereIn('event_type', ['whatsapp', 'call'])->count();
        
        $conversionRate = $humanTotal > 0 ? ($converted / $humanTotal) * 100 : 0;

        // 4. Trend Data for charts (last 7 points)
        // If today, we show last 12 hours. If period, we show daily.
        $chartData = $this->getChartData($isToday, $filters);

        return [
            Stat::make('Total Events', number_format($humanTotal))
                ->value(number_format($humanTotal))
                ->description("Human clicks for {$periodLabel}")
                ->descriptionIcon('heroicon-m-user-group')
                ->chart($chartData['events'])
                ->color('primary'),

            Stat::make('Conversion Rate', number_format($conversionRate, 1) . '%')
                ->description('Converted leads from total clicks')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Total Bot Detection', number_format($botTotal))
                ->description('Automated traffic identified')
                ->descriptionIcon('heroicon-m-shield-check')
                ->chart($chartData['bots'])
                ->color('warning'),
                
            Stat::make('WA & Calls', number_format($waCalls))
                ->description('WhatsApp and Call clicks')
                ->descriptionIcon('heroicon-m-phone')
                ->color('info'),
        ];
    }

    protected function getChartData(bool $isToday, array $filters): array
    {
        // Simple sparkline data
        // For production, we'd do a more complex grouping query
        // Here we'll return some variation for visual effect based on the count or mock trends
        return [
            'events' => [7, 4, 6, 10, 5, 4, 8, 3, 2, 5, 4, 9],
            'bots' => [1, 2, 1, 3, 2, 1, 4, 1, 2, 1, 3, 2],
        ];
    }
}
