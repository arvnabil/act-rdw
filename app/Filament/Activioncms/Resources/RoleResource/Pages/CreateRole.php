<?php

namespace App\Filament\Activioncms\Resources\RoleResource\Pages;

use App\Filament\Activioncms\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function afterCreate(): void
    {
        $data = $this->form->getRawState();
        $modules = RoleResource::getPermissionModules();
        $selectedPermissions = [];

        foreach ($modules as $key => $module) {
            if (!empty($data["permissions_{$key}"])) {
                $selectedPermissions = array_merge($selectedPermissions, $data["permissions_{$key}"]);
            }
        }

        $this->record->syncPermissions(array_unique($selectedPermissions));
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
