<?php

namespace App\Filament\Activioncms\Resources\NewsResource\Schemas;

use App\Models\News;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use App\Filament\Activioncms\Resources\SeoMetaResource\Schemas\SeoForm;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms;
use Illuminate\Support\Str;

class NewsForm
{
    public static function schema(): array
    {
        return [
            Tabs::make('Tabs')
                ->tabs([
                    Tabs\Tab::make('Content')
                        ->schema([
                            Grid::make(3)
                                ->schema([
                                    Group::make()
                                        ->columnSpan(2)
                                        ->schema([
                                            TextInput::make('title')
                                                ->required()
                                                ->live(onBlur: true)
                                                ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),

                                            TextInput::make('slug')
                                                ->dehydrated()
                                                ->required()
                                                ->unique(News::class, 'slug', ignoreRecord: true),

                                            Textarea::make('excerpt')
                                                ->rows(3)
                                                ->columnSpanFull(),

                                            RichEditor::make('content')
                                                ->required()
                                                ->columnSpanFull(),
                                        ]),

                                    Group::make()
                                        ->columnSpan(1)
                                        ->schema([
                                            Section::make('Publishing')
                                                ->schema([
                                                    Select::make('status')
                                                        ->options([
                                                            'draft' => 'Draft',
                                                            'published' => 'Published',
                                                        ])
                                                        ->default('draft')
                                                        ->required(),

                                                    DateTimePicker::make('published_at'),

                                                    Forms\Components\CheckboxList::make('categories')
                                                        ->relationship('categories', 'name')
                                                        ->searchable()
                                                        ->bulkToggleable()
                                                        ->columns(1),

                                                    Forms\Components\Select::make('tags')
                                                        ->relationship('tags', 'name')
                                                        ->multiple()
                                                        ->searchable()
                                                        ->preload()
                                                        ->createOptionForm([
                                                            Forms\Components\TextInput::make('name')
                                                                ->required()
                                                                ->live(onBlur: true)
                                                                ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                                            Forms\Components\TextInput::make('slug')
                                                                ->disabled()
                                                                ->dehydrated()
                                                                ->required()
                                                                ->unique(\App\Models\NewsTag::class, 'slug'),
                                                        ])
                                                        ->columnSpanFull(),
                                                ]),

                                            Section::make('Media')
                                                ->schema([
                                                    FileUpload::make('featured_image')
                                                        ->image()
                                                        ->disk('public')
                                                        ->directory(fn (\Filament\Schemas\Components\Utilities\Get $get) => 'news/' . ($get('slug') ?? 'temp'))
                                                        ->visibility('public')
                                                        ->required(),
                                                ]),
                                        ]),
                                ]),
                        ]),
                    Tabs\Tab::make('SEO')
                        ->schema([
                            Group::make()
                                ->relationship('seo')
                                ->schema(SeoForm::schema())
                        ]),
                ])->columnSpanFull()
        ];
    }
}
