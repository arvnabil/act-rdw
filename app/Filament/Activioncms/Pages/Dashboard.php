<?php

namespace App\Filament\Activioncms\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use BezhanSalleh\FilamentGoogleAnalytics\Widgets\SessionsWidget;
use BezhanSalleh\FilamentGoogleAnalytics\Widgets\ActiveUsersOneDayWidget;
use BezhanSalleh\FilamentGoogleAnalytics\Widgets\MostVisitedPagesWidget;
use BezhanSalleh\FilamentGoogleAnalytics\Widgets\TopReferrersListWidget;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            FilamentInfoWidget::class,
            SessionsWidget::class,
            ActiveUsersOneDayWidget::class,
            MostVisitedPagesWidget::class,
            TopReferrersListWidget::class,
        ];
    }
}
