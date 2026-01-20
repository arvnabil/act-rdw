<?php

namespace App\Filament\Activioncms\Resources\ProjectResource\Schemas;

use App\Models\Project;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use App\Filament\Activioncms\Resources\SeoMetaResource\Schemas\SeoForm;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Str;

class ProjectForm
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
                                                ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                                            TextInput::make('slug')
                                                ->disabled()
                                                ->dehydrated()
                                                ->required()
                                                ->unique(Project::class, 'slug', ignoreRecord: true),

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

                                                    Toggle::make('is_featured')
                                                        ->default(false),
                                                ]),

                                            Section::make('Media')
                                                ->schema([
                                                    FileUpload::make('thumbnail')
                                                        ->image()
                                                        ->disk('public')
                                                        ->directory(fn (\Filament\Schemas\Components\Utilities\Get $get) => 'projects/' . ($get('slug') ?? 'temp'))
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
