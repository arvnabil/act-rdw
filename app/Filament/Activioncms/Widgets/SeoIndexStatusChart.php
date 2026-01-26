<?php

namespace App\Filament\Activioncms\Widgets;

use App\Models\SeoMonitoringRecord;
use Filament\Widgets\ChartWidget;

class SeoIndexStatusChart extends ChartWidget
{
    protected ?string $heading = 'Index Coverage Status';
    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $indexed = SeoMonitoringRecord::where('is_noindex', false)->count();
        $noindex = SeoMonitoringRecord::where('is_noindex', true)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Pages',
                    'data' => [$indexed, $noindex],
                    'backgroundColor' => ['#10b981', '#f43f5e'],
                ],
            ],
            'labels' => ['Indexed', 'Noindex'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
