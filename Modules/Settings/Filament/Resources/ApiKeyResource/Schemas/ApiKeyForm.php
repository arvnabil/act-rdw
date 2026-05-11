<?php

namespace Modules\Settings\Filament\Resources\ApiKeyResource\Schemas;

use Filament\Forms\Components;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\Action;

class ApiKeyForm
{
    public static function schema(): array
    {
        return [
            Section::make('API Key Details')
                ->schema([
                    Components\TextInput::make('name')
                        ->required()
                        ->placeholder('e.g. n8n Integration')
                        ->helperText('Name this key to remember its purpose.'),
                    
                    Components\TextInput::make('key')
                        ->label('API Key')
                        ->disabled()
                        ->dehydrated(false)
                        ->visible(fn ($record) => $record !== null)
                        ->suffixAction(
                            Action::make('copyUrl')
                                ->icon('heroicon-s-clipboard')
                                ->action(fn ($record) => $record->key)
                        )
                        ->helperText('This key is generated automatically on creation.'),

                    Grid::make(3)
                        ->schema([
                            Components\Toggle::make('is_active')
                                ->label('Active')
                                ->default(true),
                            
                            Components\Toggle::make('debug_mode')
                                ->label('Debug Mode')
                                ->helperText('If enabled, full request/response payloads will be logged.')
                                ->default(false),
                            
                            Components\DateTimePicker::make('expires_at')
                                ->label('Expires At')
                                ->placeholder('Leave empty for no expiry'),
                        ]),
                ])
        ];
    }
}
