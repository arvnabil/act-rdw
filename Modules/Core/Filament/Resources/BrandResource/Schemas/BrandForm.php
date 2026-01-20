<?php

namespace Modules\Core\Filament\Resources\BrandResource\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class BrandForm
{
    public static function schema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
            Forms\Components\TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true),
            Forms\Components\FileUpload::make('image')
                ->image()
                ->disk('public')
                ->visibility('public')
                ->directory('brands')
                ->imageEditor(),
            Forms\Components\Textarea::make('desc')
                ->label('Description')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('website_url') // Fixed field name
                ->label('Website URL')
                ->url()
                ->prefix('https://'),
        ];
    }
}
