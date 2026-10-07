<?php

namespace App\Filament\Activioncms\Resources\RoleResource\Pages;

use App\Filament\Activioncms\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn () => in_array($this->record->name, ['administrator', 'co-admin', 'editor', 'viewer'])),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
