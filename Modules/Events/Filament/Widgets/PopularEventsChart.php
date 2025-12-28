<?php

namespace Modules\Events\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\Events\Models\Event;
use Illuminate\Support\Facades\DB;

class PopularEventsChart extends ChartWidget
{
    protected ?string $heading = 'Top 5 Popular Events';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $events = Event::withCount('registrations')
            ->orderByDesc('registrations_count')
            ->limit(5)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Registrations',
                    'data' => $events->pluck('registrations_count')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'
                    ],
                ],
            ],
            'labels' => $events->pluck('title')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
