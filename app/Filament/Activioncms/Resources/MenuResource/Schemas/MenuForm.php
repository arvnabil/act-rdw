<?php

namespace App\Filament\Activioncms\Resources\MenuResource\Schemas;

use App\Models\Page;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class MenuForm
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->schema([
                    Section::make('Menu Details')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            Select::make('location')
                                ->options([
                                    'primary' => 'Primary Menu',
                                    'footer' => 'Footer Menu',
                                ])
                                ->required()
                                ->unique(ignoreRecord: true),
                            Toggle::make('is_active')
                                ->default(true),
                        ])
                        ->columns(2),

                    Section::make('Menu Items')
                        ->description('Manage hierarchy. Drag to reorder.')
                        ->schema([
                            Repeater::make('items')
                                ->relationship()
                                ->schema([
                                    TextInput::make('title')->required(),
                                    Select::make('type')
                                        ->options([
                                            'page' => 'Page',
                                            'custom' => 'Custom URL',
                                        ])
                                        ->default('page')
                                        ->live(),

                                    Select::make('page_id')
                                        ->label('Page')
                                        ->options(Page::all()->pluck('title', 'id'))
                                        ->visible(fn (Get $get) => $get('type') === 'page')
                                        ->required(fn (Get $get) => $get('type') === 'page'),

                                    TextInput::make('url')
                                        ->label('URL')
                                        ->visible(fn (Get $get) => $get('type') === 'custom')
                                        ->required(fn (Get $get) => $get('type') === 'custom'),

                                    Select::make('target')
                                        ->options([
                                            '_self' => 'Same Tab',
                                            '_blank' => 'New Tab',
                                        ])
                                        ->default('_self'),

                                    // Children nesting
                                    Repeater::make('children')
                                        ->relationship()
                                        ->schema([
                                            TextInput::make('title')->required(),
                                            Select::make('type')
                                                ->options(['page' => 'Page', 'custom' => 'Custom URL'])
                                                ->default('page')
                                                ->live(),

                                            Select::make('page_id')
                                                ->options(Page::all()->pluck('title', 'id'))
                                                ->visible(fn (Get $get) => $get('type') === 'page'),

                                            TextInput::make('url')
                                                ->visible(fn (Get $get) => $get('type') === 'custom'),

                                            Select::make('target')
                                                ->options(['_self' => 'Same', '_blank' => 'New'])
                                                ->default('_self'),
                                        ])
                                        ->orderColumn('order')
                                        ->collapsible()
                                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                                ])
                                ->orderColumn('order')
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                ->columnSpanFull(),
                        ])
                ])
                ->columnSpan(['lg' => 3]),
        ];
    }
}
