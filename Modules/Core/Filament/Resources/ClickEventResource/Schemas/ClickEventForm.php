<?php

namespace Modules\Core\Filament\Resources\ClickEventResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\KeyValue;

class ClickEventForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('event_type')
                ->disabled(),
            TextInput::make('event_label'),
            TextInput::make('click_count')
                ->numeric()
                ->disabled(),
            \Filament\Forms\Components\Toggle::make('is_bot')
                ->label('Bot Status')
                ->disabled(),
            \Filament\Forms\Components\Toggle::make('is_converted')
                ->label('Converted (Sold)')
                ->helperText('Centang jika klik ini menghasilkan penjualan'),
            TextInput::make('deal_value')
                ->numeric()
                ->prefix('IDR')
                ->helperText('Nilai transaksi jika terjadi penjualan'),
            TextInput::make('utm_source'),
            TextInput::make('utm_medium'),
            TextInput::make('utm_campaign'),
            TextInput::make('device')
                ->disabled(),
            TextInput::make('city')
                ->disabled(),
            TextInput::make('region')
                ->disabled(),
            TextInput::make('country')
                ->disabled(),
            TextInput::make('page_url'),
            TextInput::make('referrer_url'),
            KeyValue::make('meta'),
        ];
    }
}
