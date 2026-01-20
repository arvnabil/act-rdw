<?php

namespace Modules\Core\Filament\Resources\ProductCategoryResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class ProductCategoryForm
{
    public static function schema(): array
    {
        return [
            Section::make('Category Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),
                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                    Toggle::make('is_active')
                        ->default(true),
                    FileUpload::make('icon')
                        ->image()
                        ->directory('product-categories')
                        ->visibility('public')
                        ->disk('public'),
                ])->columns(2)
        ];
    }
}
