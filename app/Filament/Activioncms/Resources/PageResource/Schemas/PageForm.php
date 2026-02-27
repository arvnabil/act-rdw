<?php

namespace App\Filament\Activioncms\Resources\PageResource\Schemas;

use App\Models\Page;
use Filament\Forms\Components\RichEditor;
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

                                            ...(\App\Filament\Activioncms\Schemas\BreadcrumbForm::schema('pages')),
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
