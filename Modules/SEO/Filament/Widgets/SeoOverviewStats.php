<?php

namespace Modules\SEO\Filament\Widgets;

use Modules\SEO\Models\SeoMeta;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SeoOverviewStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Calculate stats using optimized SQL to approximate SEO score
        // Score logic: 100 base - 20 (no title) - 10 (no description) - 20 (noindex)
        $stats = SeoMeta::query()
            ->selectRaw("
                COUNT(*) as total,
                CAST(SUM(CASE WHEN noindex = 0 THEN 1 ELSE 0 END) AS UNSIGNED) as indexed_pages,
                CAST(SUM(CASE WHEN title IS NULL OR title = '' OR description IS NULL OR description = '' THEN 1 ELSE 0 END) AS UNSIGNED) as missing_meta,
                CAST(AVG(100
                    - (CASE WHEN title IS NULL OR title = '' THEN 20 ELSE 0 END)
                    - (CASE WHEN description IS NULL OR description = '' THEN 10 ELSE 0 END)
                    - (CASE WHEN noindex = 1 THEN 20 ELSE 0 END)
                ) AS UNSIGNED) as avg_score,
                CAST(SUM(CASE WHEN (100
                    - (CASE WHEN title IS NULL OR title = '' THEN 20 ELSE 0 END)
                    - (CASE WHEN description IS NULL OR description = '' THEN 10 ELSE 0 END)
                    - (CASE WHEN noindex = 1 THEN 20 ELSE 0 END)
                ) < 60 THEN 1 ELSE 0 END) AS UNSIGNED) as critical_issues
            ")
            ->first();

        $avgScore = $stats->avg_score ?? 0;
        $criticalIssues = $stats->critical_issues ?? 0;
        $missingMeta = $stats->missing_meta ?? 0;

        return [
            Stat::make('Total Indexed Pages', number_format($stats->indexed_pages ?? 0))
                ->description('Pages visible to search engines')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),

            Stat::make('Pages with Missing Meta', number_format($missingMeta))
                ->description('Missing title or description')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($missingMeta > 0 ? 'warning' : 'success'),

            Stat::make('Average SEO Score', number_format($avgScore, 1))
                ->description('Based on metadata health')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($avgScore >= 80 ? 'success' : ($avgScore >= 60 ? 'warning' : 'danger')),

            Stat::make('Critical SEO Issues', number_format($criticalIssues))
                ->description('Score < 60')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($criticalIssues > 0 ? 'danger' : 'success'),
        ];
    }
}
