<?php

namespace App\Filament\Activioncms\Widgets;

use App\Models\SeoMeta;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SeoHealthChart extends ChartWidget
{
    protected ?string $heading = 'SEO Health Breakdown';
    protected static ?int $sort = 2;
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $activeFilter = $this->filter;

        $query = SeoMeta::query();

        if ($activeFilter) {
            $query->where('seoable_type', $activeFilter);
        }

        // Score logic: 100 base - 20 (no title) - 10 (no description) - 20 (noindex)
        // Ranges:
        // Good (Green): >= 80
        // Warning (Yellow): 60-79
        // Critical (Red): < 60

        $stats = $query->selectRaw('
            SUM(CASE WHEN (100 - (CASE WHEN title IS NULL THEN 20 ELSE 0 END) - (CASE WHEN description IS NULL THEN 10 ELSE 0 END) - (CASE WHEN noindex = 1 THEN 20 ELSE 0 END)) >= 80 THEN 1 ELSE 0 END) as good,
            SUM(CASE WHEN (100 - (CASE WHEN title IS NULL THEN 20 ELSE 0 END) - (CASE WHEN description IS NULL THEN 10 ELSE 0 END) - (CASE WHEN noindex = 1 THEN 20 ELSE 0 END)) BETWEEN 60 AND 79 THEN 1 ELSE 0 END) as warning,
            SUM(CASE WHEN (100 - (CASE WHEN title IS NULL THEN 20 ELSE 0 END) - (CASE WHEN description IS NULL THEN 10 ELSE 0 END) - (CASE WHEN noindex = 1 THEN 20 ELSE 0 END)) < 60 THEN 1 ELSE 0 END) as critical
        ')->first();

        return [
            'datasets' => [
                [
                    'label' => 'SEO Score',
                    'data' => [
                        $stats->good ?? 0,
                        $stats->warning ?? 0,
                        $stats->critical ?? 0
                    ],
                    'backgroundColor' => [
                        '#22c55e', // Green - 500
                        '#eab308', // Yellow - 500
                        '#ef4444', // Red - 500
                    ],
                    'hoverOffset' => 4
                ],
            ],
            'labels' => ['Good (≥80)', 'Warning (60-79)', 'Critical (<60)'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getFilters(): ?array
    {
        // Dynamically fetching types might be heavy, so we provide common ones.
        // In a real scenario, this could be cached or hardcoded if types are fixed.
        return [
            'App\\Models\\Page' => 'Pages',
            'App\\Models\\News' => 'News',
            'App\\Models\\Project' => 'Projects',
            'App\\Models\\Product' => 'Products',
        ];
    }
}
