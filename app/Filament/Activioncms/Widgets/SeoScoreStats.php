<?php

namespace App\Filament\Activioncms\Widgets;

use App\Models\SeoMeta;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SeoScoreStats extends BaseWidget
{
    protected ?string $pollingInterval = null;
    protected static bool $shouldDisplayOnDashboard = false;

    protected function getStats(): array
    {
        // Average Score
        $avgScore = round(SeoMeta::avg('seo_score') ?? 0);

        // Critical Issues (Score < 50)
        $criticalCount = SeoMeta::where('seo_score', '<', 50)->count();

        // Good Pages (Score >= 80)
        $goodCount = SeoMeta::where('seo_score', '>=', 80)->count();

        return [
            Stat::make('Average SEO Score', $avgScore . '/100')
                ->description('Site-wide average')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($avgScore >= 80 ? 'success' : ($avgScore >= 50 ? 'warning' : 'danger'))
                ->chart([$avgScore, 100]), // Simple visual

            Stat::make('Pages Needing Attention', $criticalCount)
                ->description('Score below 50')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('SEO Optimized Pages', $goodCount)
                ->description('Score 80+')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
