<?php

namespace App\Filament\Imports;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsTag;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class NewsImporter extends Importer
{
    protected static ?string $model = News::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('excerpt'),
            ImportColumn::make('content'),
            ImportColumn::make('status')
                ->rules(['in:draft,published']),
            ImportColumn::make('published_at')
                ->rules(['nullable'])
                ->castStateUsing(function ($state) {
                    if (blank($state)) return null;
                    
                    // Cleanup common issues (e.g. double spaces, leading/trailing spaces)
                    $state = preg_replace('/\s+/', ' ', trim($state));

                    $formats = [
                        'n/j/Y H:i',   // 7/16/2025 12:11
                        'n/j/Y G:i',   // 7/16/2025 12:11
                        'm/d/Y H:i',   // 07/16/2025 12:11
                        'Y-m-d H:i:s', // Standard DB
                        'd-m-Y H:i',   // Common 
                    ];

                    foreach ($formats as $format) {
                        try {
                            return Carbon::createFromFormat($format, $state);
                        } catch (\Exception $e) {
                            continue;
                        }
                    }

                    try {
                        return Carbon::parse($state);
                    } catch (\Exception $e2) {
                        return null;
                    }
                }),
            ImportColumn::make('thumbnail')
                ->label('Thumbnail (URL or path)')
                ->fillRecordUsing(fn() => null),
            ImportColumn::make('categories')
                ->fillRecordUsing(fn() => null)
                ->helperText('Comma separated category names'),
            ImportColumn::make('tags')
                ->fillRecordUsing(fn() => null)
                ->helperText('Comma separated tag names'),
            
            // SEO Data
            ImportColumn::make('seo_title')
                ->label('SEO Title')
                ->rules(['nullable', 'max:255'])
                ->fillRecordUsing(fn ($record) => null),
            ImportColumn::make('seo_description')
                ->label('SEO Description')
                ->rules(['nullable', 'max:500'])
                ->fillRecordUsing(fn ($record) => null),
            ImportColumn::make('seo_keywords')
                ->label('SEO Keywords')
                ->fillRecordUsing(fn ($record) => null),
            ImportColumn::make('og_title')
                ->label('OG Title')
                ->fillRecordUsing(fn ($record) => null),
            ImportColumn::make('og_description')
                ->label('OG Description')
                ->fillRecordUsing(fn ($record) => null),
            ImportColumn::make('og_image')
                ->label('OG Image')
                ->fillRecordUsing(fn ($record) => null),
            ImportColumn::make('canonical_url')
                ->label('Canonical URL')
                ->fillRecordUsing(fn ($record) => null),
            ImportColumn::make('noindex')
                ->boolean()
                ->label('No Index')
                ->fillRecordUsing(fn ($record) => null),
        ];
    }

    public function resolveRecord(): News
    {
        return News::firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $data = $this->data;

        try {
            // 1. Sync Categories
            if ($categoriesString = $data['categories'] ?? null) {
                $categoryNames = array_map('trim', explode(',', $categoriesString));
                $categoryIds = collect($categoryNames)->filter()->map(function ($catName) {
                    try {
                        $category = NewsCategory::firstOrCreate(
                            ['name' => Str::limit($catName, 250)],
                            ['slug' => Str::limit(Str::slug($catName), 250)]
                        );
                        return $category->id;
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("Category sync failed for '{$catName}': " . get_class($e) . " - " . $e->getMessage());
                        return null;
                    }
                })->filter()->toArray();
                
                $record->categories()->sync($categoryIds);
            }

            // 2. Sync Tags
            if ($tagsString = $data['tags'] ?? null) {
                $tagNames = array_map('trim', explode(',', $tagsString));
                $tagIds = collect($tagNames)->filter()->map(function ($tagName) {
                    try {
                        $tag = NewsTag::firstOrCreate(
                            ['name' => Str::limit($tagName, 250)],
                            ['slug' => Str::limit(Str::slug($tagName), 250)]
                        );
                        return $tag->id;
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("Tag sync failed for '{$tagName}': " . get_class($e) . " - " . $e->getMessage());
                        return null;
                    }
                })->filter()->toArray();
                
                $record->tags()->sync($tagIds);
            }

            // 3. Handle Thumbnail Auto-Download from URL
            if ($thumbnailUrl = $data['thumbnail'] ?? null) {
                $thumbnailUrl = trim($thumbnailUrl);
                if (str_starts_with($thumbnailUrl, 'http')) {
                    try {
                        $response = Http::timeout(10)->get($thumbnailUrl);
                        if ($response->successful()) {
                            $contents = $response->body();
                            $extension = pathinfo(parse_url($thumbnailUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                            $filename = $record->slug . '-activ-teknologi-' . date('Y-is') . '.' . $extension;
                            $path = 'news/' . $record->slug . '/' . $filename;
                            
                            Storage::disk('public')->put($path, $contents);
                            $record->updateQuietly(['thumbnail' => $path]);
                        }
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("Import Thumbnail failed for News {$record->id}: " . get_class($e) . " - " . $e->getMessage());
                    }
                } else if (!empty($thumbnailUrl)) {
                    $record->updateQuietly(['thumbnail' => $thumbnailUrl]);
                }
            }

            // 4. Sync SEO Metadata
            $seoData = [
                'title' => Str::limit($data['seo_title'] ?? $record->title, 190),
                'description' => Str::limit($data['seo_description'] ?? Str::limit(strip_tags($record->content), 160), 450),
                'keywords' => Str::limit($data['seo_keywords'] ?? null, 190),
                'og_title' => Str::limit($data['og_title'] ?? null, 190),
                'og_description' => Str::limit($data['og_description'] ?? null, 450),
                'og_image' => Str::limit($data['og_image'] ?? $record->thumbnail, 190),
                'canonical_url' => Str::limit($data['canonical_url'] ?? null, 190),
                'noindex' => (bool) ($data['noindex'] ?? false),
            ];

            try {
                $record->seo()->updateOrCreate(
                    ['seoable_id' => $record->id, 'seoable_type' => get_class($record)],
                    $seoData
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("SEO Meta sync failed for News {$record->id}: " . get_class($e) . " - " . $e->getMessage());
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'seo_title' => "Masalah SEO Meta: " . $e->getMessage(),
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $vEx) {
            throw $vEx;
        } catch (\Throwable $globalEx) {
            \Illuminate\Support\Facades\Log::error("Global News Import failure for News {$record->id}: " . get_class($globalEx) . " - " . $globalEx->getMessage());
            throw \Illuminate\Validation\ValidationException::withMessages([
                'title' => "Gagal memproses baris: " . $globalEx->getMessage(),
            ]);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your news import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
