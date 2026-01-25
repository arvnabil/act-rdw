<?php

namespace App\Filament\Activioncms\Resources\SettingResource\Schemas;

use Filament\Forms;

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
                ->columnSpanFull(),
        ];
    }
}
