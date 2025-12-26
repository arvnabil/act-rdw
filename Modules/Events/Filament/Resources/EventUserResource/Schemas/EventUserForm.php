<?php

namespace Modules\Events\Filament\Resources\EventUserResource\Schemas;

use Filament\Forms;

class EventUserForm
{
    public static function schema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('password')
                ->password()
                ->required(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                ->dehydrated(fn ($state) => filled($state))
                ->maxLength(255),
            Forms\Components\TextInput::make('phone')
                ->tel()
                ->maxLength(255),
            Forms\Components\FileUpload::make('avatar')
                ->image()
                ->directory('event-users')
                ->visibility('public'),
        ];
    }
}
