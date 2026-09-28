<?php

namespace Modules\Settings\Filament\Resources\ApiKeyResource\Schemas;

use Filament\Forms\Components;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Set;

class ApiKeyForm
{
    public static function schema(): array
    {
        return [
            Section::make('API Key Details')
                ->schema([
                    Components\TextInput::make('name')
                        ->required()
                        ->placeholder('e.g. Hermes MCP')
                        ->helperText('Name this key to remember its purpose.'),
                    
                    Components\CheckboxList::make('capabilities')
                        ->label('Capabilities')
                        ->options([
                            'product.read' => 'Product → Read',
                            'product.write' => 'Product → Write',
                            'product.delete' => 'Product → Delete',
                            'news.read' => 'News → Read',
                            'news.write' => 'News → Write',
                            'news.delete' => 'News → Delete',
                        ])
                        ->columns(2)
                        ->bulkToggleable()
                        ->hintAction(
                            Components\Actions\Action::make('setReadOnly')
                                ->label('Read Only MCP')
                                ->action(function (Set $set) {
                                    $set('capabilities', ['product.read', 'news.read']);
                                })
                        )
                        ->hintAction(
                            Components\Actions\Action::make('setFullAccess')
                                ->label('Full MCP Access')
                                ->requiresConfirmation()
                                ->action(function (Set $set) {
                                    $set('capabilities', ['product.read', 'product.write', 'product.delete', 'news.read', 'news.write', 'news.delete']);
                                })
                                ->color('danger')
                        ),

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