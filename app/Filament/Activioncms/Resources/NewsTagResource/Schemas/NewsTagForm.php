<?php

namespace App\Filament\Activioncms\Resources\NewsTagResource\Schemas;

use App\Models\NewsTag;
use Filament\Forms;
use Illuminate\Support\Str;

class NewsTagForm
{
    public static function schema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
            Forms\Components\TextInput::make('slug')
                ->disabled()
                ->dehydrated()
                ->required()
                ->unique(NewsTag::class, 'slug', ignoreRecord: true),
        ];
    }
}
