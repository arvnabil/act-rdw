<?php

namespace Modules\ServiceSolutions\Filament\Resources\ServiceResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\RichEditor;
use App\Filament\Activioncms\Resources\SeoMetaResource\Schemas\SeoForm;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function schema(): array
    {
        return [
            Tabs::make('Tabs')
                ->tabs([
                    Tab::make('Review & Content')
                        ->schema([
                            Section::make('General Information')
                                ->schema([
                                    TextInput::make('name')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn($state, $set) => $set('slug', Str::slug($state))),
                                    TextInput::make('slug')
                                        ->dehydrated()
                                        ->required()
                                        ->unique('services', 'slug', ignoreRecord: true),
                                    Textarea::make('description')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                    Textarea::make('excerpt')
                                        ->label('Excerpt (Short Summary)')
                                        ->rows(2)
                                        ->columnSpanFull(),
                                    TextInput::make('sort_order')
                                        ->label('Sort Order')
                                        ->numeric()
                                        ->default(0),
                                ])->columns(2),

                            Section::make('Page Content')
                                ->schema([
                                    RichEditor::make('content')
                                        ->label('Detailed Content')
                                        ->columnSpanFull(),
                                ]),

                            Section::make('Visuals & Branding')
                                ->schema([
                                    FileUpload::make('featured_image')
                                        ->label('Featured Hero Image')
                                        ->image()
                                        ->directory('services/featured')
                                        ->disk('public')
                                        ->visibility('public')
                                        ->columnSpanFull(),
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
                        ]),
                    Tab::make('SEO')
                        ->schema([
                            Group::make()
                                ->relationship('seo')
                                ->schema(SeoForm::schema())
                        ]),
                ])->columnSpanFull()
        ];
    }
}
