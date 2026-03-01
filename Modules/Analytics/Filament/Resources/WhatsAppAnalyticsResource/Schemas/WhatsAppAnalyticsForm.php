<?php

namespace Modules\Analytics\Filament\Resources\WhatsAppAnalyticsResource\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class WhatsAppAnalyticsForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('target_value')->label('Phone/Target'),
            TextInput::make('cta_position'),
            TextInput::make('utm_source'),
            TextInput::make('utm_medium'),
            TextInput::make('utm_campaign'),
            TextInput::make('page_url'),
            TextInput::make('referrer_url'),
            \Filament\Forms\Components\KeyValue::make('meta'),
        ];
    }
}
