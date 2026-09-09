<?php

namespace Modules\Campaign\Filament\Resources\CampaignResource\Pages;

use Modules\Campaign\Filament\Resources\CampaignResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;

class EditCampaign extends EditRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Preview — opens in new tab
            Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn () => route('campaign.preview', $this->record->id))
                ->openUrlInNewTab(),

            // Publish
            Action::make('publish')
                ->label('Publish')
                ->icon('heroicon-o-globe-alt')
                ->color('success')
                ->visible(fn () => $this->record->status !== 'published')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status'       => 'published',
                        'published_at' => $this->record->published_at ?? Carbon::now(),
                        'updated_by'   => auth()->id(),
                    ]);
                    $this->refreshFormData(['status', 'published_at']);
                    Notification::make()->title('Campaign published')->success()->send();
                }),

            // Unpublish
            Action::make('unpublish')
                ->label('Unpublish')
                ->icon('heroicon-o-pause-circle')
                ->color('warning')
                ->visible(fn () => $this->record->status === 'published')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status'     => 'unpublished',
                        'updated_by' => auth()->id(),
                    ]);
                    $this->refreshFormData(['status']);
                    Notification::make()->title('Campaign unpublished')->warning()->send();
                }),

            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();
        return $data;
    }
}