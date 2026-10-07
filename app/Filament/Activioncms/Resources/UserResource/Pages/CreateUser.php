<?php

namespace App\Filament\Activioncms\Resources\UserResource\Pages;

use App\Filament\Activioncms\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string \ = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return \->getResource()::getUrl('index');
    }
}
