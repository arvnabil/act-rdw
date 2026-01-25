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
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use App\Filament\Activioncms\Resources\SeoMetaResource\Schemas\SeoForm;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Utilities\Get;

class ProductForm
{
    public static function schema(): array
    {
        return [
            Tabs::make('ProductTabs')
                ->tabs([
                    Tab::make('Product Details')
                        ->schema([
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
                                                ->preload()
                                                ->live(),
                                            Select::make('brand_id')
                                                ->relationship('brand', 'name')
                                                ->required()
                                                ->searchable()
                                                ->preload(),
                                            Select::make('product_category_id')
                                                ->relationship('category', 'name')
                                                ->label('Device Category')
                                                ->searchable()
                                                ->preload()
                                                ->createOptionForm(\Modules\Core\Filament\Resources\ProductCategoryResource\Schemas\ProductCategoryForm::schema()),
                                            TextInput::make('price')
                                                ->numeric()
                                                ->prefix('IDR'),
                                            CheckboxList::make('solutions')
                                                ->relationship('solutions', 'title', modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('service_id', $get('service_id')))
                                                ->label('Applicable Solutions')
                                                ->columns(2)
                                                ->gridDirection('row')
                                                ->visible(fn (Get $get) => filled($get('service_id')))
                                                ->columnSpanFull(),
                                            TextInput::make('solution_type')
                                                ->label('Legacy Type (Deprecated)')
                                                ->visible(false),
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
                                            \Filament\Forms\Components\Repeater::make('features')
                                                ->schema([
                                                    TextInput::make('name')->required(),
                                                    TextInput::make('value')->label('Description')->required(),
                                                    TextInput::make('additional')->label('Additional Info'),
                                                ])
                                                ->columns(3),
                                            RichEditor::make('specification_text')
                                                ->columnSpanFull(),
                                            RichEditor::make('features_text')
                                                ->columnSpanFull(),
                                        ]),

                                    Section::make('Marketplace Links')
                                        ->schema([
                                            TextInput::make('link_accommerce')->label('Online Purchase Link (Acommerce)')->url(),
                                            TextInput::make('whatsapp_note')
                                                ->label('WhatsApp Message Note')
                                                ->placeholder('e.g. Halo, saya tertarik dengan produk ini...')
                                                ->helperText('Custom message pre-filled when user clicks "Best Price Request".'),
                                        ])->columns(1),
                                ])->columnSpan(['lg' => 2]),

                            Group::make()
                                ->schema([
                                    Section::make('Image')
                                        ->schema([
                                            FileUpload::make('image_path')
                                                ->image()
                                                ->disk('public')
                                                ->visibility('public')
                                                ->directory('products'),
                                        ]),
                                    Section::make('Status')
                                        ->schema([
                                            Toggle::make('is_active')
                                                ->required()
                                                ->default(true),
                                        ]),
                                ])->columnSpan(['lg' => 1]),
                        ])
                        ->columns(3),

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
