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

                                            Section::make('Project Information')
                                                ->schema([
                                                    TextInput::make('client')
                                                        ->label('Client Name'),
                                                    TextInput::make('category')
                                                        ->label('Category'),
                                                    \Filament\Forms\Components\DatePicker::make('project_date'),
                                                    Textarea::make('address')
                                                        ->rows(2),
                                                    FileUpload::make('download_brochures')
                                                        ->label('Download Brochure')
                                                        ->multiple()
                                                        ->maxFiles(2)
                                                        ->maxSize(2048) // 2MB
                                                        ->disk('public')
                                                        ->directory('project-brochures')
                                                        ->visibility('public')
                                                        ->helperText('Max 2 files, 2MB each.'),
                                                ])->columns(2),
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

                                            Section::make('Contact Widget')
                                                ->schema([
                                                    // Static Widget: Title and Phone are now hardcoded in frontend.
                                                    // Only Message Note is editable.

                                                    Textarea::make('whatsapp_note')
                                                        ->label('WhatsApp Message Note')
                                                        ->placeholder('Hi, I am interested in this project...')
                                                        ->rows(3)
                                                        ->helperText('This message will be pre-filled when the user clicks the WhatsApp button. The number is set in Settings.'),
                                                ])->collapsible(),


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
