<?php

namespace Modules\Events\Filament\Resources\EventCategoryResource\Schemas;

use Filament\Forms;

use Illuminate\Support\Str;

class EventCategoryForm
{
    public static function schema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
            Forms\Components\TextInput::make('slug')
                ->required()
                ->disabled()
                ->unique(ignoreRecord: true),
            Forms\Components\Toggle::make('is_active')
                ->default(true)
                ->required(),
        ];
    }
}
