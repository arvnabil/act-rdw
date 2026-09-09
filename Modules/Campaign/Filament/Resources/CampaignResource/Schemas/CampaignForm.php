<?php

namespace Modules\Campaign\Filament\Resources\CampaignResource\Schemas;

use Modules\Campaign\Models\Campaign;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Illuminate\Support\Str;

class CampaignForm
{
    public static function schema(): array
    {
        return [
            Tabs::make('Campaign')
                ->tabs([

                    // ─── Tab: General ─────────────────────────────────────
                    Tabs\Tab::make('General')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Grid::make(3)->schema([
                                Group::make()->columnSpan(2)->schema([
                                    TextInput::make('name')
                                        ->label('Campaign Name')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),

                                    TextInput::make('slug')
                                        ->label('Slug')
                                        ->required()
                                        ->maxLength(255)
                                        ->unique(Campaign::class, 'slug', ignoreRecord: true)
                                        ->helperText('Public URL: /campaign/{slug}')
                                        ->prefix('/campaign/'),
                                ]),

                                Group::make()->columnSpan(1)->schema([
                                    Section::make('Publishing')->schema([
                                        Select::make('status')
                                            ->options([
                                                'draft'       => 'Draft',
                                                'published'   => 'Published',
                                                'unpublished' => 'Unpublished',
                                            ])
                                            ->default('draft')
                                            ->required(),

                                        DateTimePicker::make('starts_at')
                                            ->label('Starts At')
                                            ->nullable()
                                            ->helperText('Optional: campaign becomes active at this time'),

                                        DateTimePicker::make('ends_at')
                                            ->label('Ends At')
                                            ->nullable()
                                            ->helperText('Optional: campaign expires at this time'),
                                    ]),
                                ]),
                            ]),
                        ]),

                    // ─── Tab: Content ─────────────────────────────────────
                    Tabs\Tab::make('Content')
                        ->icon('heroicon-o-code-bracket')
                        ->schema([
                            Textarea::make('content_html')
                                ->label('HTML Content')
                                ->rows(24)
                                ->columnSpanFull()
                                ->helperText('Paste your full campaign HTML here. All CSS, JavaScript, and assets will be preserved as-is.')
                                ->placeholder('<!DOCTYPE html><html>...</html>'),
                        ]),

                    // ─── Tab: SEO ─────────────────────────────────────────
                    Tabs\Tab::make('SEO')
                        ->icon('heroicon-o-magnifying-glass')
                        ->schema([
                            Section::make('Search Engine Optimization')->schema([
                                TextInput::make('seo_data.meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(70)
                                    ->helperText('Recommended: 50–70 characters'),

                                Textarea::make('seo_data.meta_description')
                                    ->label('Meta Description')
                                    ->rows(3)
                                    ->maxLength(160)
                                    ->helperText('Recommended: 120–160 characters'),

                                TextInput::make('seo_data.canonical_url')
                                    ->label('Canonical URL')
                                    ->url()
                                    ->maxLength(500)
                                    ->placeholder('Leave blank to use default /campaign/{slug}'),

                                Select::make('seo_data.robots')
                                    ->label('Robots')
                                    ->options([
                                        'index, follow'     => 'Index, Follow (default)',
                                        'noindex, follow'   => 'Noindex, Follow',
                                        'index, nofollow'   => 'Index, Nofollow',
                                        'noindex, nofollow' => 'Noindex, Nofollow',
                                    ])
                                    ->default('index, follow'),
                            ]),

                            Section::make('Open Graph')->schema([
                                TextInput::make('seo_data.og_title')
                                    ->label('OG Title')
                                    ->maxLength(95),

                                Textarea::make('seo_data.og_description')
                                    ->label('OG Description')
                                    ->rows(3)
                                    ->maxLength(200),

                                FileUpload::make('seo_data.og_image')
                                    ->label('OG Image')
                                    ->image()
                                    ->disk('public')
                                    ->directory('campaigns/og')
                                    ->imagePreviewHeight('150'),
                            ]),
                        ]),

                    // ─── Tab: GEO ─────────────────────────────────────────
                    Tabs\Tab::make('GEO')
                        ->icon('heroicon-o-sparkles')
                        ->label('GEO')
                        ->schema([
                            Section::make('Generative Engine Optimization')
                                ->description('Provide structured information to help AI search engines understand this campaign.')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextInput::make('geo_data.entity.name')
                                            ->label('Primary Entity Name')
                                            ->placeholder('e.g. Yealink MeetingBar A40'),

                                        Select::make('geo_data.entity.type')
                                            ->label('Entity Type')
                                            ->options([
                                                'Product'      => 'Product',
                                                'Service'      => 'Service',
                                                'Organization' => 'Organization',
                                                'Event'        => 'Event',
                                                'Brand'        => 'Brand',
                                                'Other'        => 'Other',
                                            ]),
                                    ]),

                                    Textarea::make('geo_data.summary')
                                        ->label('Summary')
                                        ->rows(4)
                                        ->placeholder('Clear, concise description of the campaign subject...')
                                        ->columnSpanFull(),

                                    Repeater::make('geo_data.key_facts')
                                        ->label('Key Facts')
                                        ->simple(TextInput::make('fact')->placeholder('e.g. Supports 4K video'))
                                        ->addActionLabel('Add Fact')
                                        ->defaultItems(0)
                                        ->collapsible(),

                                    Repeater::make('geo_data.benefits')
                                        ->label('Benefits')
                                        ->simple(TextInput::make('benefit')->placeholder('e.g. Easy plug-and-play setup'))
                                        ->addActionLabel('Add Benefit')
                                        ->defaultItems(0)
                                        ->collapsible(),

                                    Repeater::make('geo_data.use_cases')
                                        ->label('Use Cases')
                                        ->simple(TextInput::make('use_case')->placeholder('e.g. Huddle room video conference'))
                                        ->addActionLabel('Add Use Case')
                                        ->defaultItems(0)
                                        ->collapsible(),

                                    Repeater::make('geo_data.faq')
                                        ->label('FAQ')
                                        ->schema([
                                            TextInput::make('question')
                                                ->label('Question')
                                                ->required()
                                                ->columnSpanFull(),
                                            Textarea::make('answer')
                                                ->label('Answer')
                                                ->required()
                                                ->rows(3)
                                                ->columnSpanFull(),
                                        ])
                                        ->addActionLabel('Add FAQ Item')
                                        ->defaultItems(0)
                                        ->collapsible()
                                        ->columnSpanFull(),

                                    Textarea::make('geo_data.organization_context')
                                        ->label('Organization Context')
                                        ->rows(3)
                                        ->placeholder('Brief context about your organization in relation to this campaign...')
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ])
                ->columnSpanFull()
                ->persistTabInQueryString(),
        ];
    }
}