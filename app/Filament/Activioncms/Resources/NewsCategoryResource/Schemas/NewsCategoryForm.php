<?php

namespace App\Filament\Activioncms\Resources\NewsCategoryResource\Schemas;

use App\Models\NewsCategory;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class NewsCategoryForm
{
    public static function schema(): array
    {
        return [
            \Filament\Schemas\Components\Tabs::make('NewsCategory')
                ->tabs([
                    \Filament\Schemas\Components\Tabs\Tab::make('General')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                            Forms\Components\TextInput::make('slug')
                                ->disabled()
                                ->dehydrated()
                                ->required()
                                ->unique(NewsCategory::class, 'slug', ignoreRecord: true),
                        ]),
                    \Filament\Schemas\Components\Tabs\Tab::make('SEO')
                        ->schema([
                            \Filament\Schemas\Components\Group::make()
                                ->relationship('seo')
                                ->schema(\App\Filament\Activioncms\Resources\SeoMetaResource\Schemas\SeoForm::schema())
                        ]),
                ])->columnSpanFull()
        ];
    }
}
