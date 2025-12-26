<?php

namespace Modules\Events\Filament\Resources\OrganizerResource\Schemas;

use Filament\Forms;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;

class OrganizerForm
{
    public static function schema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
            Forms\Components\TextInput::make('email')
                ->email()
                ->maxLength(255),
            Forms\Components\TextInput::make('phone')
                ->tel()
                ->maxLength(255),
            Forms\Components\TextInput::make('slug')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            Forms\Components\FileUpload::make('logo')
                ->image()
                ->directory('organizers')
                ->visibility('public'),
            Forms\Components\TextInput::make('website')
                ->url()
                ->maxLength(255),
            Forms\Components\Toggle::make('is_active')
                ->required()
                ->default(true),
            Forms\Components\Textarea::make('description')
                ->columnSpanFull(),
        ];
    }
}
