<?php

namespace App\Filament\Activioncms\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use BezhanSalleh\GoogleAnalytics\Widgets\SessionsWidget;
use BezhanSalleh\GoogleAnalytics\Widgets\ActiveUsersOneDayWidget;
use BezhanSalleh\GoogleAnalytics\Widgets\MostVisitedPagesWidget;
use BezhanSalleh\GoogleAnalytics\Widgets\TopReferrersListWidget;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        $widgets = [
            AccountWidget::class,
            FilamentInfoWidget::class,
        ];

        if (config('analytics.property_id') && class_exists(SessionsWidget::class)) {
            $widgets = array_merge($widgets, [
                SessionsWidget::class,
                ActiveUsersOneDayWidget::class,
                MostVisitedPagesWidget::class,
                TopReferrersListWidget::class,
            ]);
        }

        return $widgets;
    }
}
