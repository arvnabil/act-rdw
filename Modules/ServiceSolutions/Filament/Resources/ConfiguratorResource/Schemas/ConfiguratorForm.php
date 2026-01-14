<?php

namespace Modules\ServiceSolutions\Filament\Resources\ConfiguratorResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class ConfiguratorForm
{
    public static function schema(): array
    {
        return [
            Section::make('Basic Information')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Textarea::make('description')
                        ->columnSpanFull(),

                    FileUpload::make('image')
                        ->image()
                        ->directory('configurators'),

                    Toggle::make('is_active')
                        ->required()
                        ->default(true),
                ])->columns(2),
        ];
    }
}
