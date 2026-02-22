<?php

namespace App\Filament\Activioncms\Resources\SeoWhitelistDomainResource\Pages;

use App\Filament\Activioncms\Resources\SeoWhitelistDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeoWhitelistDomain extends EditRecord
{
    protected static string $resource = SeoWhitelistDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
