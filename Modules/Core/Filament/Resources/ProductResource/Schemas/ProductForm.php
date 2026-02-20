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
                                                ->live()
                                                ->afterStateUpdated(fn (Set $set) => $set('solutions', [])),
                                            Select::make('brand_id')
                                                ->relationship('brand', 'name')
                                                ->required()
                                                ->searchable()
                                                ->preload(),
                                            Select::make('categories')
                                                ->relationship('categories', 'name')
                                                ->label('Device Categories')
                                                ->multiple()
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
                                                ->columnSpanFull()
                                                ->fileAttachmentsAcceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'])
                                                ->fileAttachmentsMaxSize(2048)
                                                ->fileAttachmentsDisk('public')
                                                ->fileAttachmentsDirectory(fn ($get) => 'products/' . ($get('slug') ?? 'default') . '/media-descriptions')
                                                ->fileAttachmentsVisibility('public'),
                                               
                                        ])->columns(2),

                                    Section::make('Specifications & Features')
                                        ->schema([
                                            TagsInput::make('tags'),
                                            \Filament\Forms\Components\Repeater::make('specs')
                                                ->label('Specifications (Grouped)')
                                                ->schema([
                                                    TextInput::make('group_name')
                                                        ->label('Group Name')
                                                        ->required()
                                                        ->placeholder('e.g. Dimensions, Audio, etc.'),
                                                    \Filament\Forms\Components\Repeater::make('items')
                                                        ->label('Items')
                                                        ->schema([
                                                            TextInput::make('key')->required(),
                                                            TextInput::make('value')->required(),
                                                        ])
                                                        ->columns(2)
                                                        ->defaultItems(0)
                                                        ->cloneable()
                                                        ->collapsible()
                                                        ->required(),
                                                ])
                                                ->itemLabel(fn (array $state): ?string => $state['group_name'] ?? null)
                                                ->collapsible()
                                                ->cloneable()
                                                ->columns(2)
                                                ->columnSpanFull(),
                                            \Filament\Forms\Components\Repeater::make('features')
                                                ->schema([
                                                    TextInput::make('name')->required(),
                                                    TextInput::make('value')->label('Description')->required(),
                                                    TextInput::make('additional')->label('Additional Info'),
                                                ])
                                                ->columns(3),
                                            RichEditor::make('specification_text')
                                                ->columnSpanFull()
                                                ->fileAttachmentsAcceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'])
                                                ->fileAttachmentsMaxSize(2048)
                                                ->fileAttachmentsDisk('public')
                                                ->fileAttachmentsDirectory(fn ($get) => 'products/' . ($get('slug') ?? 'default') . '/media-specifications')
                                                ->fileAttachmentsVisibility('public'),
                                                
                                            RichEditor::make('features_text')
                                                ->columnSpanFull()
                                                ->fileAttachmentsAcceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'])
                                                ->fileAttachmentsMaxSize(2048)
                                                ->fileAttachmentsDisk('public')
                                                ->fileAttachmentsDirectory(fn ($get) => 'products/' . ($get('slug') ?? 'default') . '/media-features')
                                                ->fileAttachmentsVisibility('public'),
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
                                                ->maxSize(2048)
                                                ->downloadable()
                                                ->openable()
                                                ->helperText('Nama file akan otomatis disesuaikan (Contoh: nama-produk.png). Ukuran maks: 2MB.')
                                                ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, Get $get): string {
                                                    $slug = $get('slug') ?: 'temp';
                                                    return \App\Helpers\UploadHelper::getSluggedFilename($file, 'products/' . $slug);
                                                }),
                                        ]),
                                            Section::make('Status')
                                         ->schema([
                                             Toggle::make('is_active')
                                                 ->required()
                                                 ->default(true),
                                             Toggle::make('is_featured')
                                                 ->label('Featured Product')
                                                 ->default(false),
                                             Toggle::make('is_new')
                                                 ->label('New Arrival')
                                                 ->default(false),
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
