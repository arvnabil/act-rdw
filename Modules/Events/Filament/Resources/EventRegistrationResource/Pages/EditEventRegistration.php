<?php

namespace Modules\Events\Filament\Resources\EventRegistrationResource\Pages;

use Modules\Events\Filament\Resources\EventRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventRegistration extends EditRecord
{
    protected static string $resource = EventRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
