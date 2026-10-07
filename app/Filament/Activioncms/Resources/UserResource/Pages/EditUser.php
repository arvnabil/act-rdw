<?php

namespace App\Filament\Activioncms\Resources\UserResource\Pages;

use App\Filament\Activioncms\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string \ = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn() => \->record->id === auth()->id()),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return \->getResource()::getUrl('index');
    }
}
