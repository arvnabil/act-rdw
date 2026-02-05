<?php

namespace App\Filament\Activioncms\Resources\SeoMetaResource\Pages;

use App\Filament\Activioncms\Resources\SeoMetaResource;
use Filament\Resources\Pages\ListRecords;

class ListSeoMetas extends ListRecords
{
    protected static string $resource = SeoMetaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('cleanOrphans')
                ->label('Clean Orphans')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->tooltip('Remove SEO records that are no longer linked to any page')
                ->action(function () {
                    $deletedCount = \App\Models\SeoMeta::all()
                        ->filter(fn ($record) => is_null($record->seoable))
                        ->each->delete()
                        ->count();
                    
                    if ($deletedCount > 0) {
                        \Filament\Notifications\Notification::make()
                            ->title("Deleted {$deletedCount} orphaned SEO records.")
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title("No orphaned records found.")
                            ->info()
                            ->send();
                    }
                })
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Activioncms\Widgets\SeoOverviewStats::class,
            \App\Filament\Activioncms\Widgets\SeoHealthChart::class,
            \App\Filament\Activioncms\Widgets\TopSeoIssues::class,
        ];
    }
}
