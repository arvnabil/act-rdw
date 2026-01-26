<?php

namespace App\Filament\Activioncms\Resources\NewsTagResource\Pages;

use App\Filament\Activioncms\Resources\NewsTagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNewsTags extends EditRecord
{
    protected static string $resource = NewsTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
