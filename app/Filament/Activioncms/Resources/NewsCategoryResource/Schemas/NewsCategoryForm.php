<?php

namespace App\Filament\Activioncms\Resources\NewsCategoryResource\Schemas;

use App\Models\NewsCategory;
use Filament\Forms;
use Illuminate\Support\Str;

class NewsCategoryForm
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
                ->unique(NewsCategory::class, 'slug', ignoreRecord: true),
        ];
    }
}
