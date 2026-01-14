<?php

namespace Modules\ServiceSolutions\Filament\Resources\ServiceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\ServiceSolutions\Filament\Resources\ServiceResource;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
