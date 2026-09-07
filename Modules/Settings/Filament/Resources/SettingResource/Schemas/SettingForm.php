<?php

namespace Modules\Settings\Filament\Resources\SettingResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Modules\Settings\Models\Setting;

class SettingForm
{
    public static function schema(): array
    {
        return [
            Forms\Components\TextInput::make('key')
                ->required()
                ->unique(ignoreRecord: true)
                ->helperText('Unique key for the setting (e.g., whatsapp_number)'),
            Forms\Components\TextInput::make('label')
                ->required()
                ->helperText('Human readable label (e.g., WhatsApp Number)'),
            Forms\Components\Textarea::make('value')
                ->columnSpanFull()
                ->helperText(fn (Get $get): string => Setting::isSecretKey((string) ($get('key') ?? ''))
                    ? 'Secret value. The stored secret is never displayed. Leave blank to keep the existing value, or enter a new value to replace it.'
                    : 'The value for this setting.'),
        ];
    }
}
