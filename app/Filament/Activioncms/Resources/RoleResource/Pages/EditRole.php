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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $rolePermissions = $this->record->permissions->pluck('name')->toArray();
        $modules = RoleResource::getPermissionModules();

        foreach ($modules as $key => $module) {
            $data["permissions_{$key}"] = array_values(
                array_intersect(array_keys($module['permissions']), $rolePermissions)
            );
        }

        return $data;
    }

    protected function afterSave(): void
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
