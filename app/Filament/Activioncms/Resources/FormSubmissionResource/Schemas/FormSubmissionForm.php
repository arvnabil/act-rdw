<?php

namespace App\Filament\Activioncms\Resources\FormSubmissionResource\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;

class FormSubmissionForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('form_key')
                ->label('Form Key')
                ->readOnly(),
            TextInput::make('page_slug')
                ->label('Page Slug')
                ->readOnly(),
            KeyValue::make('payload')
                ->label('Submission Data')
                ->columnSpanFull()
                ->disabled(),
            TextInput::make('ip_address')
                ->label('IP Address')
                ->readOnly(),
            TextInput::make('user_agent')
                ->label('User Agent')
                ->readOnly()
                ->columnSpanFull(),
        ];
    }
}
