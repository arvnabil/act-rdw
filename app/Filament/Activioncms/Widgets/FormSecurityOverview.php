<?php

namespace App\Filament\Activioncms\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class FormSecurityOverview extends BaseWidget
{
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        // 1. Total Views (Last 30 Days)
        $totalViews = DB::table('form_analytics')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // 2. Total Submissions (Last 30 Days - Allowed + Blocked)
        $totalSubmissions = DB::table('form_security_logs')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // 3. Blocked Spam
        $blockedSpam = DB::table('form_security_logs')
            ->where('decision', 'block')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // 4. Spam Rate
        $spamRate = $totalSubmissions > 0 ? round(($blockedSpam / $totalSubmissions) * 100, 1) : 0;

        // 5. Conversion Rate (Allowed Submissions / Views)
        $allowedSubmissions = $totalSubmissions - $blockedSpam;
        $conversionRate = $totalViews > 0 ? round(($allowedSubmissions / $totalViews) * 100, 1) : 0;

        // 6. Security Mode (Check one form as sample or show global status)
        // Let's rely on the most active form settings
        $settings = DB::table('form_security_settings')->orderBy('updated_at', 'desc')->first();
        $securityMode = $settings ? ($settings->auto_managed ? 'Auto-Pilot' : 'Manual') : 'Manual';
        $captchaStatus = $settings ? ($settings->captcha_enabled ? 'ON' : 'OFF') : 'ON';

        return [
            Stat::make('Form Views (30d)', number_format($totalViews))
                ->description('Total page views on contact sections')
                ->descriptionIcon('heroicon-m-eye')
                ->color('gray'),

            Stat::make('Conversion Rate', $conversionRate . '%')
                ->description("$allowedSubmissions legitimate submissions")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Spam Blocked', number_format($blockedSpam))
                ->description("Spam Rate: $spamRate%")
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('danger'),

            Stat::make('Security Engine', "$securityMode ($captchaStatus)")
                ->description('Auto-decision system status')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('primary'),
        ];
    }
}
