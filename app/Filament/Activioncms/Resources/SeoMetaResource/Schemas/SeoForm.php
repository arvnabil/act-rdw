<?php

namespace App\Filament\Activioncms\Resources\SeoMetaResource\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

use Filament\Schemas\Components\View;

class SeoForm
{
    public static function schema(): array
    {
        return [
            Section::make('Search Engine Optimization (SEO)')
                ->description('Manage meta tags, social sharing preview, and indexing settings.')
                ->schema([
                    // React SERP Preview
                    View::make('filament.activioncms.components.react-serp-wrapper')
                        ->columnSpanFull(),

                    Grid::make(1)->schema([
                        TextInput::make('title')
                            ->label('Meta Title')
                            ->helperText('Recommended: 50-60 characters.')
                            ->maxLength(255) // Relaxed from 60
                            ->live(debounce: 500),

                        Textarea::make('description')
                            ->label('Meta Description')
                            ->helperText('Recommended: 150-160 characters.')
                            ->maxLength(500) // Relaxed from 250
                            ->rows(2)
                            ->live(debounce: 500),

                        TextInput::make('keywords')
                            ->label('Keywords')
                            ->helperText('Comma separated (e.g. Technology, AI).')
                            ->placeholder('Technology, AI, Coding'),

                        TextInput::make('canonical_url')
                            ->label('Canonical URL')
                            ->rule('url') // Use backend rule instead of ->url() to avoid HTML5 type="url" validation issues in hidden tabs
                            ->nullable()
                            ->helperText('Leave empty to use the default page URL. Use this to prevent duplicate content issues.')
                            ->live(debounce: 500),
                    ]),

                    Section::make('Social Media (Open Graph)')
                        ->schema([
                            TextInput::make('og_title')
                                ->label('OG Title')
                                ->helperText('Default: Meta Title.'),

                            Textarea::make('og_description')
                                ->label('OG Description')
                                ->rows(2)
                                ->helperText('Default: Meta Description.'),

                            FileUpload::make('og_image')
                                ->label('Social Share Image')
                                ->image()
                                ->disk('public')
                                ->visibility('public')
                                ->maxSize(2048)
                                ->downloadable()
                                ->openable()
                                ->helperText('Nama file akan otomatis disesuaikan (Contoh: seo-slug.png). Ukuran maks: 2MB.')
                                ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, Get $get): string {
                                    $slug = $get('../../slug') ?: ($get('../../title') ?: ($get('title') ?: 'seo'));
                                    return \App\Helpers\UploadHelper::getSluggedFilename($file, 'seo/' . $slug);
                                }),
                        ])
                        ->collapsible(),

                    Section::make('Advanced Settings & JSON-LD')
                        ->schema([
                            Toggle::make('noindex')
                                ->label('No Index')
                                ->helperText('Ask search engines NOT to index this page.')
                                ->default(false),

                            Textarea::make('schema_override')
                                ->label('JSON-LD Structured Data')
                                ->helperText('Auto-generated schema. Click "Generate" to lock in a snapshot, or "Clear" to use dynamic generation.')
                                ->rows(10)
                                ->columnSpanFull()
                                ->extraAttributes(['class' => 'font-mono text-sm'])
                                ->hintAction(
                                    Action::make('generate_json_ld')
                                        ->label('Generate JSON-LD')
                                        ->icon('heroicon-o-code-bracket')
                                        ->action(function (Get $get, Set $set, $record) {
                                            if (!$record || !$record->seoable) {
                                                Notification::make()
                                                    ->title('Please save the page first before generating JSON-LD.')
                                                    ->warning()
                                                    ->send();
                                                return;
                                            }

                                            $seoService = app(\App\Services\Seo\SeoService::class);
                                            try {
                                                // Create a fresh manager for snapshot generation
                                                $seoManager = new \App\Services\Seo\SeoManager();
                                                $data = $seoService->extract($record->seoable);

                                                // Build the graph using the same logic as the ViewComposer
                                                $seoManager->addSchema(new \App\Services\Seo\Schemas\WebPageSchema(
                                                    $data['title'], $data['description'], $data['url'], $seoManager->getWebsiteId()
                                                ));

                                                if ($data['type'] === 'Article' && isset($data['article'])) {
                                                    $seoManager->addSchema(new \App\Services\Seo\Schemas\ArticleSchema(
                                                        $data['title'], $data['description'], $data['url'], $data['og_image'],
                                                        ['@type' => 'Organization', 'name' => config('app.name')],
                                                        ['@id' => $seoManager->getOrganizationId()],
                                                        $data['article']['datePublished'], $data['article']['dateModified'],
                                                        $data['keywords'], $data['article']['section']
                                                    ));
                                                }

                                                $graph = $seoManager->getGraph();
                                                $jsonString = json_encode(['@context' => 'https://schema.org', '@graph' => $graph], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

                                                $set('schema_override', $jsonString);

                                                Notification::make()
                                                    ->title('JSON-LD Generated Successfully!')
                                                    ->success()
                                                    ->send();
                                            } catch (\Exception $e) {
                                                Notification::make()
                                                    ->title('Error generating JSON-LD')
                                                    ->body($e->getMessage())
                                                    ->danger()
                                                    ->send();
                                            }

                                        })
                                )
                                ->hintAction(
                                    Action::make('clear_json_ld')
                                        ->label('Clear (Use Dynamic)')
                                        ->icon('heroicon-o-trash')
                                        ->color('danger')
                                        ->action(fn (Set $set) => $set('schema_override', null))
                                        ->requiresConfirmation()
                                ),
                        ])
                        ->collapsed(),
                ])
        ];
    }
}
