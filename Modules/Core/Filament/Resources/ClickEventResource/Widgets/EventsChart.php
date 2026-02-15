<?php

namespace Modules\Core\Filament\Resources\ClickEventResource\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\Core\Models\AnalyticsClickEvent;
use Illuminate\Support\Facades\DB;

class EventsChart extends ChartWidget
{
    protected ?string $heading = 'Daily Events (Last 30 Days)';

    protected function getData(): array
    {
        // Get data for last 30 days
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();

        $data = AnalyticsClickEvent::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Fill missing dates with 0
        $chartData = [];
        $labels = [];
        for ($i = 0; $i < 30; $i++) {
            $date = $start->copy()->addDays($i)->format('Y-m-d');
            $labels[] = $start->copy()->addDays($i)->format('d M');
            $chartData[] = $data[$date] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Events',
                    'data' => $chartData,
                    'fill' => 'start',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
