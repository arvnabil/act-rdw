<?php

namespace Modules\Settings\Filament\Resources\SettingResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Settings\Filament\Resources\SettingResource;
use Modules\Settings\Models\Setting;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    /**
     * Never send stored secret values to the browser via Livewire state.
     * Secret fields are blanked here; the admin sees a masked/empty state.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        if ($record && Setting::isSecretKey((string) $record->getAttribute('key'))) {
            $data['value'] = null;
        }

        return $data;
    }

    /**
     * Blank secret submissions mean "keep the current value". Only a real
     * replacement value is persisted (and encrypted by the model).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->getRecord();

        if ($record && Setting::isSecretKey((string) $record->getAttribute('key')) && blank($data['value'] ?? null)) {
            unset($data['value']);
        }

        return $data;
    }
}
