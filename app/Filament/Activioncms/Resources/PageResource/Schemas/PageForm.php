<?php

namespace App\Filament\Activioncms\Resources\PageResource\Schemas;

use App\Models\Page;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Activioncms\Resources\SeoMetaResource\Schemas\SeoForm;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Str;

class PageForm
{
    public static function schema(string $layout = 'default'): array
    {
        return [
            Tabs::make('PageTabs')
                ->tabs([
                    Tab::make('Content')
                        ->schema([
                            Grid::make(['default' => 12])
                                ->schema([
                                    Section::make('Page Information')
                                        ->schema([
                                            TextInput::make('title')
                                                ->required()
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),

                                            TextInput::make('slug')
                                                ->dehydrated()
                                                ->required()
                                                ->unique(Page::class, 'slug', ignoreRecord: true),

                                            Select::make('type')
                                                ->options([
                                                    'home' => 'Home',
                                                    'static' => 'Static',
                                                    'dynamic' => 'Dynamic',
                                                    'brand' => 'Brand',
                                                    'service' => 'Service',
                                                    'project' => 'Project',
                                                ])
                                                ->default('static')
                                                ->required(),

                                            Toggle::make('is_homepage')
                                                ->label('Set as Homepage')
                                                ->helperText('This will make this page the landing page of the site.'),

                                            Toggle::make('show_breadcrumb')
                                                ->label('Show Breadcrumb')
                                                ->default(true),
                                        ])
                                        ->columns(2)
                                        ->columnSpan(['lg' => 8]),

                                    Section::make('Publishing')
                                        ->schema([
                                            Select::make('status')
                                                ->options([
                                                    'draft' => 'Draft',
                                                    'published' => 'Published',
                                                ])
                                                ->default('draft')
                                                ->required(),
                                        ])
                                        ->columnSpan(['lg' => 4]),
                                ]),

                            Section::make('Page Sections')
                                ->description('Manage the sections of this page. Drag to reorder.')
                                ->schema([
                                    Repeater::make('sections')
                                        ->relationship()
                                        ->schema([
                                            Select::make('section_key')
                                                ->label('Section Type')
                                                ->options([
                                                    'slider' => 'Advanced Slider', // New Generic Slider
                                                    'brand_partners' => 'Brand Partners',
                                                    'about' => 'About Section',

                                                    // Services Page Specific
                                                    'service_list' => 'Services List (Grid)',
                                                    'service_solution' => 'Services Solution',
                                                    'service_cta' => 'Services CTA',
                                                    'service_clients' => 'Services Clients',

                                                    'services' => 'Services List',
                                                    'projects' => 'Projects Showcase',
                                                    'news' => 'Latest News',
                                                    'why_choose_us' => 'Why Choose Us',
                                                    'cta' => 'Call to Action',
                                                    'about_content' => 'About Content (Legacy)',
                                                    'vision_mission' => 'Vision & Mission (Legacy)',
                                                    'testimonial' => 'Testimonial (Legacy)',
                                                    'contact' => 'Contact (Legacy)',
                                                ])
                                                ->required()
                                                ->live(),

                                            Toggle::make('is_active')
                                                ->default(true),

                                            // --- SLIDER CONFIGURATION ---
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

                                            // --- ABOUT SECTION ---
                                            Group::make()
                                                ->schema([
                                                    TextInput::make('config.title')->label('Title'),
                                                    TextInput::make('config.subtitle')->label('Subtitle'),
                                                    Textarea::make('config.description')->rows(3),
                                                    FileUpload::make('config.images')
                                                        ->image()
                                                        ->multiple()
                                                        ->disk('public')
                                                        ->directory('about-images')
                                                        ->visibility('public')
                                                        ->imageEditor(),
                                                    // Features Repeater
                                                    Repeater::make('config.features')
                                                        ->label('Features')
                                                        ->schema([
                                                            TextInput::make('title')->required(),
                                                            Textarea::make('text')->rows(2),
                                                            FileUpload::make('icon')
                                                                ->image()
                                                                ->disk('public')
                                                                ->directory('icons')
                                                                ->visibility('public')
                                                                ->preserveFilenames(),
                                                        ])
                                                        ->collapsible()
                                                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                                ])
                                                ->visible(fn (Get $get) => $get('section_key') === 'about'),

                                            // --- WHY CHOOSE US ---
                                            Group::make()
                                                ->schema([
                                                    TextInput::make('config.title')->label('Title'),
                                                    TextInput::make('config.subtitle')->label('Subtitle'),
                                                    Textarea::make('config.description')->rows(3),
                                                    TextInput::make('config.video_url')
                                                        ->url()
                                                        ->label('Video URL')
                                                        ->prefixIcon('heroicon-o-video-camera'),
                                                    FileUpload::make('config.images')
                                                        ->image()
                                                        ->multiple()
                                                        ->disk('public')
                                                        ->directory('why-choose-us-images')
                                                        ->visibility('public'),
                                                    Repeater::make('config.features')
                                                        ->label('Features')
                                                        ->schema([
                                                            TextInput::make('title')->required(),
                                                            Textarea::make('text')->rows(2),
                                                            FileUpload::make('icon')->image()->disk('public')->directory('icons')->visibility('public'),
                                                        ])
                                                        ->collapsible()
                                                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                                ])
                                                ->visible(fn (Get $get) => $get('section_key') === 'why_choose_us'),

                                            // --- BRAND PARTNERS ---
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
                                                        ->grid(4)
                                                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                                                ])
                                                ->visible(fn (Get $get) => $get('section_key') === 'brand_partners'),

                                            // --- CONTENT LISTS (Services, Projects, News) ---
                                            Group::make()
                                                ->schema([
                                                    TextInput::make('config.title')->label('Section Title')->placeholder('Auto-generated if empty'),
                                                    TextInput::make('config.subtitle')->label('Subtitle'),
                                                    Grid::make(2)->schema([
                                                        TextInput::make('config.limit')
                                                            ->label('Item Limit')
                                                            ->numeric()
                                                            ->default(6),
                                                        Select::make('config.order')
                                                            ->options(['asc' => 'Oldest', 'desc' => 'Newest', 'random' => 'Random'])
                                                            ->default('asc'),
                                                    ])
                                                ])
                                                ->visible(fn (Get $get) => in_array($get('section_key'), ['services', 'projects', 'news'])),

                                            // --- CTA / CLIENTS ---
                                            Group::make()
                                                ->schema([
                                                    TextInput::make('config.title')->default('Our Clients'),
                                                    TextInput::make('config.subtitle')->default('Clients'),
                                                    TagsInput::make('config.clients')
                                                        ->label('Client Codes')
                                                        ->helperText('Enter client codes (e.g. 1_1, 1_2)')
                                                        ->placeholder('1_1'),
                                                ])
                                                ->visible(fn (Get $get) => $get('section_key') === 'cta'),


                                        ])
                                        ->orderColumn('position')
                                        ->itemLabel(fn (array $state): ?string => $state['section_key'] ?? null)
                                        ->collapsible()
                                        ->collapsed(),
                                ]),
                        ]),
                    Tab::make('SEO')
                        ->schema([
                            Group::make()
                                ->relationship('seo')
                                ->schema(SeoForm::schema())
                        ]),
                ])
                ->columnSpanFull()
        ];
    }
}
