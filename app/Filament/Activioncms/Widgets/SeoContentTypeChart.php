<?php

namespace App\Filament\Activioncms\Widgets;

use App\Models\SeoMonitoringRecord;
use Filament\Widgets\ChartWidget;

class SeoContentTypeChart extends ChartWidget
{
    protected ?string $heading = 'URLs per Content Type';
    protected ?string $maxHeight = '250px';
    protected static bool $shouldDisplayOnDashboard = false;

    protected function getData(): array
    {
        $data = SeoMonitoringRecord::query()
            ->selectRaw('model, count(*) as count')
            ->groupBy('model')
            ->pluck('count', 'model')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Count',
                    'data' => array_values($data),
                    'backgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
