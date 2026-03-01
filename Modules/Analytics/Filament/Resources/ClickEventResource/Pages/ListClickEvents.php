<?php

namespace Modules\Analytics\Filament\Resources\ClickEventResource\Pages;

use Modules\Analytics\Filament\Resources\ClickEventResource;
use Filament\Resources\Pages\ListRecords;

class ListClickEvents extends ListRecords
{
    protected static string $resource = ClickEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('clearBot')
                ->label('Clear Bot')
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->tooltip('Remove all click events detected as bots')
                ->action(function () {
                    $deletedCount = \Modules\Analytics\Models\AnalyticsClickEvent::where('is_bot', true)->delete();
                    
                    if ($deletedCount > 0) {
                        \Filament\Notifications\Notification::make()
                            ->title("Deleted {$deletedCount} bot records.")
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title("No bot records found.")
                            ->info()
                            ->send();
                    }
                })
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ClickEventResource\Widgets\ClickEventsOverview::class,
            ClickEventResource\Widgets\EventsChart::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}
