<?php

namespace Modules\ServiceSolutions\Filament\Resources\ServiceResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;

class ServiceForm
{
     public static function schema(): array
    {
        return [
                Section::make('General Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique('services', 'slug', ignoreRecord: true),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),

                Section::make('Visuals & Branding')
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->image()
                            ->directory('services/thumbnails')
                            ->disk('public')
                            ->visibility('public'),
                        FileUpload::make('icon')
                            ->image()
                            ->directory('services/icons')
                            ->disk('public')
                            ->visibility('public'),
                        TextInput::make('hero_subtitle')
                            ->helperText('Subtitle in the hero section of the detail page'),
                        TextInput::make('grid_title')
                            ->helperText('Title above the solutions grid in the detail page'),
                    ])->columns(2),
            ];
    }
}
