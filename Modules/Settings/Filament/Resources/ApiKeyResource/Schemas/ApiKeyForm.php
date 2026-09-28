<?php

namespace Modules\Settings\Filament\Resources\ApiKeyResource\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;

class ApiKeyForm
{
    public static function schema(): array
    {
        return [
            Section::make('API Key Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->placeholder('e.g. Hermes MCP')
                        ->helperText('Beri nama key ini agar mudah diingat fungsinya.'),

                    TextInput::make('key')
                        ->label('API Key (Secret Token)')
                        ->disabled()
                        ->dehydrated(false)
                        ->visible(fn ($record) => $record !== null)
                        ->helperText('Token API key lengkap. Anda bisa langsung copy dari field ini.'),
                    
                    CheckboxList::make('capabilities')
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
                            Action::make('setReadOnly')
                                ->label('Read Only MCP')
                                ->action(function (Set $set) {
                                    $set('capabilities', ['product.read', 'news.read']);
                                })
                        )
                        ->hintAction(
                            Action::make('setFullAccess')
                                ->label('Full MCP Access')
                                ->requiresConfirmation()
                                ->action(function (Set $set) {
                                    $set('capabilities', ['product.read', 'product.write', 'product.delete', 'news.read', 'news.write', 'news.delete']);
                                })
                                ->color('danger')
                        ),

                    Grid::make(3)
                        ->schema([
                            Toggle::make('is_active')
                                ->label('Active')
                                ->default(true),
                            
                            Toggle::make('debug_mode')
                                ->label('Debug Mode')
                                ->helperText('Jika aktif, seluruh payload request & response akan dicatat di log.')
                                ->default(false),
                            
                            DateTimePicker::make('expires_at')
                                ->label('Expires At')
                                ->placeholder('Kosongkan jika tanpa batas waktu (no expiry)'),
                        ]),
                ])
        ];
    }
}