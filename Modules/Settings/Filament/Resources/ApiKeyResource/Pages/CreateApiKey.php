<?php

namespace Modules\Settings\Filament\Resources\ApiKeyResource\Pages;

use Modules\Settings\Filament\Resources\ApiKeyResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CreateApiKey extends CreateRecord
{
    protected static string $resource = ApiKeyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $key = $this->record->key;
        Notification::make()
            ->title('API Key Created')
            ->body(new HtmlString("Copy this key now. It will not be shown again.<br><br><strong>{$key}</strong>"))
            ->success()
            ->persistent()
            ->send();
    }
}