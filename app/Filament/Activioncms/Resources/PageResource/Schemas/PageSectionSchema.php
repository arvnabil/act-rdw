<?php

namespace App\Filament\Activioncms\Resources\PageResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;

/*
|--------------------------------------------------------------------------
| CORE SYSTEM FILE - CONTRACT DEFINITION
|--------------------------------------------------------------------------
| This file defines the Input Contract for the CMS.
| Removing fields here will cause Data Loss or Frontend Errors.
*/
class PageSectionSchema
{
    public static function getSliderSchema(): array
    {
        return [
            Group::make()
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Select::make('config.variant')
                                ->options([
                                    'hero' => 'Hero Slider',
                                    'testimonial' => 'Testimonial Slider',
                                ])
                                ->default('hero')
                                ->required(),

                            Select::make('config.source')
                                ->options([
                                    'static' => 'Manual Entry',
                                    'query' => 'Database Query',
                                ])
                                ->default('static')
                                ->live()
                                ->required(),

                            TextInput::make('config.delay')
                                ->numeric()
                                ->default(5000)
                                ->label('Autoplay Delay (ms)'),
                        ]),

                    // Static Slides Repeater
                    Repeater::make('config.slides')
                        ->label('Slides')
                        ->visible(fn (Get $get) => $get('config.source') === 'static')
                        ->schema([
                            FileUpload::make('bg_image')
                                ->label('Background Image')
                                ->image()
                                ->disk('public')
                                ->directory('hero-slides')
                                ->visibility('public')
                                ->visible(fn (Get $get) => $get('../../config.variant') === 'hero'),

                            FileUpload::make('image')
                                ->label('Image/Logo')
                                ->image()
                                ->disk('public')
                                ->directory('slider-images')
                                ->visibility('public'),

                            TextInput::make('title')->label('Title'),
                            Textarea::make('description')->label('Description'),
                            TextInput::make('url')->label('Link URL'),

                            // Buttons Repeater for Hero
                            Repeater::make('buttons')
                                ->visible(fn (Get $get) => $get('../../config.variant') === 'hero')
                                ->schema([
                                    TextInput::make('text')->required(),
                                    TextInput::make('url')->required(),
                                    Select::make('style')
                                        ->options(['style2' => 'White', 'style7' => 'Blue'])
                                        ->default('style7'),
                                ])
                                ->columns(3)
                        ])
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),

                    // Query Configuration
                    Grid::make(2)
                        ->visible(fn (Get $get) => $get('config.source') === 'query')
                        ->schema([
                            Select::make('config.query.model')
                                ->options([
                                    'services' => 'Services',
                                    'projects' => 'Projects',
                                    'posts' => 'News/Posts',
                                ])
                                ->required(),

                            TextInput::make('config.query.limit')
                                ->numeric()
                                ->default(5),

                            Select::make('config.query.order')
                                ->options(['asc' => 'Oldest', 'desc' => 'Newest', 'random' => 'Random'])
                                ->default('desc'),
                        ])
                ])
                ->visible(fn (Get $get) => $get('section_key') === 'slider'),
        ];
    }

    public static function getAboutSchema(): array
    {
        return [
            Group::make()
                ->schema([
                    TextInput::make('config.title')->label('Section Title')->required(),
                    TextInput::make('config.subtitle')->label('Subtitle'),
                    RichEditor::make('config.description')
                        ->label('Content'),

                    FileUpload::make('config.images')
                        ->label('Images (Gallery)')
                        ->multiple()
                        ->image()
                        ->disk('public')
                        ->directory('about-images')
                        ->visibility('public'),

                    Repeater::make('config.features')
                        ->label('Feature Points')
                        ->schema([
                            TextInput::make('title')->required(),
                            Textarea::make('text'),
                            FileUpload::make('icon')->image()->disk('public')->directory('icons')->visibility('public'),
                        ])
                        ->columns(2)
                ])
                ->visible(fn (Get $get) => $get('section_key') === 'about'),
        ];
    }

    public static function getServicesSchema(): array
    {
        return [
            Group::make()
                ->schema([
                    TextInput::make('config.limit')
                        ->label('Number of Items')
                        ->numeric()
                        ->default(6),

                    Select::make('config.order')
                        ->options(['asc' => 'Oldest', 'desc' => 'Newest', 'random' => 'Random'])
                        ->default('asc'),
                ])
                ->visible(fn (Get $get) => $get('section_key') === 'services'),
        ];
    }

    public static function getProjectsSchema(): array
    {
        return [
             Group::make()
                ->schema([
                    TextInput::make('config.limit')
                        ->label('Number of Items')
                        ->numeric()
                        ->default(8),
                ])
                ->visible(fn (Get $get) => $get('section_key') === 'projects'),
        ];
    }

    public static function getNewsSchema(): array
    {
        return [
             Group::make()
                ->schema([
                    TextInput::make('config.title')->default('Latest News'),
                    TextInput::make('config.subtitle')->default('Blog'),
                    TextInput::make('config.limit')
                        ->label('Number of Posts')
                        ->numeric()
                        ->default(3),
                ])
                ->visible(fn (Get $get) => $get('section_key') === 'news'),
        ];
    }

    public static function getWhyChooseUsSchema(): array
    {
        return [
            Group::make()
                ->schema([
                    TextInput::make('config.title')->label('Title')->required(),
                    TextInput::make('config.subtitle')->label('Subtitle'),
                    Textarea::make('config.description')->label('Description'),
                    TextInput::make('config.video_url')->label('YouTube Video URL')->url(),

                    FileUpload::make('config.images')
                        ->label('Side Images')
                        ->multiple()
                        ->maxFiles(2)
                        ->image()
                        ->disk('public')
                        ->directory('wcu-images')
                        ->visibility('public'),

                    Repeater::make('config.features')
                        ->label('Why Choose Us Points')
                        ->schema([
                            TextInput::make('title')->required(),
                            Textarea::make('text'),
                            FileUpload::make('icon')->image()->disk('public')->directory('icons')->visibility('public'),
                        ])
                        ->columns(2)
                ])
                ->visible(fn (Get $get) => $get('section_key') === 'why_choose_us'),
        ];
    }

    public static function getCTASchema(): array
    {
        return [
            Group::make()
                ->schema([
                     TextInput::make('config.title')->label('Title')->default('Our Clients'),
                     TextInput::make('config.subtitle')->label('Subtitle')->default('Partners'),

                     // For simple client list demo, let's use tags or a simple repeater if needed,
                     // but the seeder uses a simple array of strings. TagsInput is good for that.
                     TagsInput::make('config.clients')
                        ->label('Client Codes/Names')
                        ->helperText('Enter client logo codes (e.g. 1_1, 1_2) or names that map to assets.')
                        ->separator(','),
                ])
                ->visible(fn (Get $get) => $get('section_key') === 'cta'),
        ];
    }

    public static function getBrandPartnersSchema(): array
    {
        return [
            Group::make()
                ->schema([
                    TextInput::make('config.title')->label('Title'),
                    TextInput::make('config.subtitle')->label('Subtitle'),

                    Repeater::make('config.brands')
                        ->label('Brand Logos')
                        ->schema([
                            FileUpload::make('image')
                                ->image()
                                ->disk('public')
                                ->directory('brand-images')
                                ->visibility('public')
                                ->required(),
                            TextInput::make('name')->label('Brand Name'),
                            TextInput::make('url')->label('Website URL'),
                        ])
                        ->collapsible()
                        ->columns(3) // Slightly different display for schema view if needed
                ])
                ->visible(fn (Get $get) => $get('section_key') === 'brand_partners'),
        ];
    }
}
