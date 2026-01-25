<?php

namespace Modules\Core\Filament\Resources\BrandResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class BrandForm
{
    public static function schema(): array
    {
        return [
            Tabs::make('Brand')
                ->tabs([
                    Tabs\Tab::make('General')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                            TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true),
                            FileUpload::make('image')
                                ->image()
                                ->disk('public')
                                ->visibility('public')
                                ->directory('brands')
                                ->imageEditor(),
                            Textarea::make('desc')
                                ->label('Description')
                                ->columnSpanFull(),
                            TextInput::make('website_url')
                                ->label('Website URL')
                                ->url()
                                ->prefix('https://'),
                        ]),
                    Tabs\Tab::make('Landing Page')
                        ->schema([
                            Section::make('Hero Configuration')
                                ->schema([
                                    Toggle::make('enabled')
                                        ->label('Enable Hero Section')
                                        ->default(true),
                                    TextInput::make('eyebrow')
                                        ->label('Eyebrow Text')
                                        ->placeholder('AUTHORIZED PARTNER')
                                        ->columnSpanFull(),
                                    TextInput::make('title')
                                        ->label('Hero Title'),
                                    TextInput::make('subtitle')
                                        ->label('Hero Subtitle')
                                        ->placeholder('Business Solution'),
                                    Textarea::make('desc')
                                        ->label('Hero Description')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                    TextInput::make('cta_label')
                                        ->label('CTA Button Label')
                                        ->placeholder('Contact Sales'),
                                    TextInput::make('cta_url')
                                        ->label('CTA Button URL')
                                        ->placeholder('#products')
                                        ->prefix('https:// or #'),
                                    FileUpload::make('background_image')
                                        ->label('Hero Background Image')
                                        ->image()
                                        ->disk('public')
                                        ->directory('brands/hero')
                                        ->imageEditor()
                                        ->columnSpanFull(),
                                    Repeater::make('awards')
                                        ->label('Award & Certified Badges')
                                        ->schema([
                                            FileUpload::make('image')
                                                ->label('Badge Image')
                                                ->image()
                                                ->disk('public')
                                                ->directory('brands/awards')
                                                ->required(),
                                            TextInput::make('alt')
                                                ->label('Alt Text')
                                                ->placeholder('Award Name'),
                                        ])
                                        ->grid(4)
                                        ->columnSpanFull()
                                        ->collapsible(),
                                ])->columns(2)->statePath('landing_config.hero'),
                            Section::make('Service Solutions')
                                ->schema([
                                    // Configuration Group
                                    Section::make('Configuration')
                                        ->schema([
                                            Toggle::make('enabled')
                                                ->label('Enable Solutions Section')
                                                ->default(true),
                                            TextInput::make('title')
                                                ->label('Section Title')
                                                ->placeholder('Our Solutions'),
                                        ])
                                        ->statePath('landing_config.solutions'),

                                    // Relationship Management using Select
                                    // Use 'Select' with 'multiple' and 'relationship'
                                    \Filament\Forms\Components\Select::make('serviceSolutions')
                                        ->label('Linked Solutions')
                                        ->relationship('serviceSolutions', 'title')
                                        ->multiple()
                                        ->preload()
                                        ->searchable()
                                        ->columnSpanFull()
                                        ->helperText('Select which service solutions belong to this brand.'),
                                ]),
                            Section::make('Category List')
                                ->schema([
                                    Toggle::make('enabled')
                                        ->label('Enable Category List')
                                        ->default(true),
                                    TextInput::make('title')
                                        ->label('Section Title')
                                        ->placeholder('All Categories'),
                                ])->statePath('landing_config.categories'),
                            Section::make('Work Showcase')

                                ->schema([
                                    Toggle::make('enabled')
                                        ->label('Enable Work Showcase')
                                        ->default(true),
                                    TextInput::make('title')
                                        ->label('Section Title')
                                        ->placeholder('Work Showcase'),
                                    Repeater::make('items')
                                        ->label('Showcase Items')
                                        ->schema([
                                            TextInput::make('title')->required(),
                                            TextInput::make('category')->required(),
                                            FileUpload::make('image')
                                                ->image()
                                                ->disk('public')
                                                ->directory('brands/showcase')
                                                ->required(),
                                        ])
                                        ->columns(2)
                                        ->grid(2)
                                        ->collapsible(),
                                ])->statePath('landing_config.showcase'),
                            Section::make('Latest Products')
                                ->schema([
                                    Toggle::make('enabled')
                                        ->label('Enable Latest Products')
                                        ->default(true),
                                    TextInput::make('title')
                                        ->label('Section Title')
                                        ->placeholder('Latest Products'),
                                    TextInput::make('count')
                                        ->label('Number of Products')
                                        ->numeric()
                                        ->default(8),
                                ])->statePath('landing_config.products'),
                        ])->columnSpanFull(),
                ])->columnSpanFull(),
        ];
    }
}
