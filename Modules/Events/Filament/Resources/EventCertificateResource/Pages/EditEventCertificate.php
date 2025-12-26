<?php

namespace Modules\Events\Filament\Resources\EventCertificateResource\Pages;

use Modules\Events\Filament\Resources\EventCertificateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventCertificate extends EditRecord
{
    protected static string $resource = EventCertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
