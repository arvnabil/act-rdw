<?php

namespace Modules\Events\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Modules\Events\Models\EventRegistration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RegistrationTrendChart extends ChartWidget
{
    protected ?string $heading = 'Registrations per Day (Last 30 Days)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Get data for last 30 days
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();

        $data = EventRegistration::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
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
                    'label' => 'New Registrations',
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
