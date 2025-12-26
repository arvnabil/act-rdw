<?php

namespace Modules\Events\Filament\Resources\EventDocumentationResource\Pages;

use Modules\Events\Filament\Resources\EventDocumentationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventDocumentation extends EditRecord
{
    protected static string $resource = EventDocumentationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
