<?php

namespace Modules\Core\Filament\Resources\ProductResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class ProductForm
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->schema([
                    Section::make('Basic Information')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true),
                            TextInput::make('sku')
                                ->label('SKU'),
                            Select::make('service_id')
                                ->relationship('service', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),
                            Select::make('brand_id')
                                ->relationship('brand', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),
                            TextInput::make('price')
                                ->numeric()
                                ->prefix('IDR'),
                            TextInput::make('solution_type'),
                            TextInput::make('datasheet_url')
                                ->url(),
                            RichEditor::make('description')
                                ->columnSpanFull(),
                        ])->columns(2),

                    Section::make('Specifications & Features')
                        ->schema([
                            TagsInput::make('tags'),
                            KeyValue::make('specs')
                                ->keyLabel('Spec Name')
                                ->valueLabel('Value'),
                            KeyValue::make('features')
                                ->keyLabel('Feature')
                                ->valueLabel('Description'),
                            RichEditor::make('specification_text')
                                ->columnSpanFull(),
                            RichEditor::make('features_text')
                                ->columnSpanFull(),
                        ]),

                    Section::make('Marketplace Links')
                        ->schema([
                            TextInput::make('link_accommerce')->label('Accocommerce')->url(),
                        ])->columns(1),
                ])->columnSpan(['lg' => 2]),

            Group::make()
                ->schema([
                    Section::make('Image')
                        ->schema([
                            FileUpload::make('image_path')
                                ->image()
                                ->directory('products'),
                        ]),
                    Section::make('Status')
                        ->schema([
                            Toggle::make('is_active')
                                ->required()
                                ->default(true),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ];
    }
}
