<?php

namespace Modules\ServiceSolutions\Filament\Resources\ConfiguratorResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;

class StepsForm
{
    public static function schema(): array
    {
        return [
            TextInput::make('name')
                ->label('Internal Name (Stepper)')
                ->required()
                ->maxLength(255),

            TextInput::make('title')
                ->label('Page Title (H4)')
                ->maxLength(255),

            Textarea::make('description')
                ->label('Subtitle')
                ->columnSpanFull(),

            TextInput::make('sort_order')
                ->numeric()
                ->default(0),

            Repeater::make('questions')
                ->relationship()
                ->label('Questions')
                ->schema([
                    TextInput::make('label')->required(),
                    TextInput::make('variable_name')->required(),
                    Select::make('type')
                        ->options([
                            'card_selection' => 'Card Selection',
                            'card_multi_selection' => 'Card Multi Selection',
                            'service_checklist' => 'Service Checklist',
                            'select' => 'Dropdown',
                            'quantity_input' => 'Quantity Input',
                        ])
                        ->required(),
                    Toggle::make('is_mandatory')->default(true),
                    KeyValue::make('conditions')
                        ->label('Show if...')
                        ->keyLabel('Variable')
                        ->valueLabel('Value'),

                    Repeater::make('options')
                        ->relationship()
                        ->schema([
                            TextInput::make('label')->required(),
                            TextInput::make('value')->required(),
                            Select::make('service_solution_id')
                                ->label('Link to Service Solution (Optional)')
                                ->options(\Modules\ServiceSolutions\Models\ServiceSolution::pluck('title', 'id'))
                                ->searchable()
                                ->preload(),
                            Group::make()
                                ->schema([
                                    TextInput::make('metadata.price')
                                        ->numeric()
                                        ->prefix('IDR'),
                                    FileUpload::make('metadata.image')
                                        ->image()
                                        ->disk('public')
                                        ->visibility('public')
                                        ->maxSize(2048)
                                        ->placeholder('')
                                        ->imagePreviewHeight('250')
                                        ->extraAttributes([
                                            'class' => '[&_.filepond--drop-label]:hidden',
                                        ])
                                        ->helperText('Ukuran maks: 2MB.')
                                        ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, Get $get): string {
                                            $stepName = $get('../../name') ?: 'step';
                                            return \App\Helpers\UploadHelper::getSluggedFilename($file, 'configurator-images/' . $stepName);
                                        }),

                                    Group::make()
                                        ->schema([
                                            TextInput::make('metadata.image_width')
                                                ->label('Image Width')
                                                ->placeholder('80px')
                                                ->default('80px'),
                                            TextInput::make('metadata.image_height')
                                                ->label('Image Height')
                                                ->placeholder('80px')
                                                ->default('80px'),
                                        ])->columns(2),
                                    Textarea::make('metadata.description')
                                        ->rows(2),
                                    Toggle::make('metadata.recommended'),

                                    Repeater::make('metadata.attributes')
                                        ->label('Generic Attributes (Badges, Lists, Links, Images)')
                                        ->schema([
                                            Select::make('type')
                                                ->options([
                                                    'badge' => 'Badge',
                                                    'icon_text' => 'Icon + Text',
                                                    'list' => 'List Items',
                                                    'link' => 'Link / Button',
                                                    'image' => 'Image',
                                                    'separator' => 'Separator',
                                                ])
                                                ->reactive()
                                                ->required(),

                                            // Fields for Text/Badge/link
                                            TextInput::make('text')
                                                ->hidden(fn (Get $get) => in_array($get('type'), ['separator', 'list', 'image'])),

                                            Select::make('color')
                                                ->options([
                                                    'primary' => 'Primary (Blue)',
                                                    'success' => 'Success (Green)',
                                                    'warning' => 'Warning (Yellow)',
                                                    'danger' => 'Danger (Red)',
                                                    'info' => 'Info (Cyan)',
                                                    'dark' => 'Dark',
                                                    'muted' => 'Muted (Gray)',
                                                ])
                                                ->hidden(fn (Get $get) => !in_array($get('type'), ['badge', 'icon_text'])),

                                            TextInput::make('icon')
                                                ->placeholder('fa-solid fa-star')
                                                ->hidden(fn (Get $get) => !in_array($get('type'), ['icon_text', 'link'])),

                                            // Fields for Link
                                            TextInput::make('url')
                                                ->label('URL')
                                                ->hidden(fn (Get $get) => !in_array($get('type'), ['link'])),
                                            Select::make('style')
                                                ->options(['button' => 'Button', 'text_link' => 'Text Link'])
                                                ->default('button')
                                                ->hidden(fn (Get $get) => !in_array($get('type'), ['link'])),

                                            // Fields for List
                                            TagsInput::make('items')
                                                ->label('List Items (Press Enter)')
                                                ->hidden(fn (Get $get) => $get('type') !== 'list'),

                                            // Fields for Image
                                            FileUpload::make('url')
                                                ->label('Upload Image')
                                                ->image()
                                                ->disk('public')
                                                ->visibility('public')
                                                ->maxSize(2048)
                                                ->placeholder('')
                                                ->imagePreviewHeight('250')
                                                ->extraAttributes([
                                                    'class' => '[&_.filepond--drop-label]:hidden',
                                                ])
                                                ->helperText('Ukuran maks: 2MB.')
                                                ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, Get $get): string {
                                                    $stepName = $get('../../../name') ?: 'attribute';
                                                    return \App\Helpers\UploadHelper::getSluggedFilename($file, 'configurator-attributes/' . $stepName);
                                                })
                                                ->hidden(fn (Get $get) => $get('type') !== 'image'),
                                            TextInput::make('alt')
                                                ->label('Alt Text')
                                                ->hidden(fn (Get $get) => $get('type') !== 'image'),
                                        ])
                                        ->columnSpanFull()
                                        ->grid(2)
                                        ->collapsible(),

                                    // Keep KeyValue for other random metadata purely as backup
                                    KeyValue::make('metadata_extra')
                                        ->label('Other Metadata (Advanced)')
                                ]),
                            KeyValue::make('conditions')
                                ->label('Show if...'),
                        ])
                        ->collapsed()
                ])
                ->columnSpanFull()
                ->collapsed(),
        ];
    }
}
