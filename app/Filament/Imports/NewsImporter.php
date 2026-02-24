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

            // 3. Handle Thumbnail Auto-Download from URL or Local Fallback
            if (isset($data['thumbnail'])) {
                $thumbnailPath = trim($data['thumbnail']);
                
                if (empty($thumbnailPath) || strtoupper($thumbnailPath) === 'DELETE') {
                    $record->update(['thumbnail' => null]);
                } else {
                    $contents = null;
                    $sourceType = 'unknown';

                    // Level 1: Check if it's a URL
                    if (str_starts_with($thumbnailPath, 'http')) {
                        // Check if it's already a local URL
                        $localPath = \App\Helpers\ImageHelper::getLocalPathFromUrl($thumbnailPath);
                        if ($localPath) {
                            \Illuminate\Support\Facades\Log::info("NewsImporter: Detected local URL '{$thumbnailPath}', skipping download and using existing file: {$localPath}");
                            $record->update(['thumbnail' => $localPath]);
                            $contents = null; // Skip further processing
                        } else {
                            \Illuminate\Support\Facades\Log::info("NewsImporter: Detected external URL '{$thumbnailPath}', starting migration to local storage.");
                            try {
                                // Robust URL handling: encode spaces
                                $cleanUrl = str_replace(' ', '%20', $thumbnailPath);
                                
                                $response = Http::withoutVerifying()
                                    ->withHeaders([
                                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
                                        'Accept' => 'image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8'
                                    ])
                                    ->timeout(60)
                                    ->retry(3, 1000)
                                    ->get($cleanUrl);
                                
                                \Illuminate\Support\Facades\Log::info("NewsImporter: Download response status: " . $response->status());

                                if ($response->successful()) {
                                    $contents = $response->body();
                                    $sourceType = 'URL';
                                    \Illuminate\Support\Facades\Log::info("NewsImporter: Download successful, size: " . strlen($contents) . " bytes");
                                } else {
                                    \Illuminate\Support\Facades\Log::warning("NewsImporter: URL download failed for {$thumbnailPath}. Status: " . $response->status());
                                }
                            } catch (\Throwable $e) {
                                \Illuminate\Support\Facades\Log::info("NewsImporter: URL download attempt failed for {$thumbnailPath}. Error: " . $e->getMessage());
                            }
                        }
                    }

                    // Level 2: Local Fallback (Direct Upload)
                    // If URL failed OR if it was just a filename (e.g. "myimage.jpg")
                    if (!$contents) {
                        $filename = basename($thumbnailPath);
                        $localPath = 'import-news/' . $filename;
                        
                        if (Storage::disk('public')->exists($localPath)) {
                            $contents = Storage::disk('public')->get($localPath);
                            $sourceType = 'Local (import-news/)';
                        }
                    }

                    if ($contents) {
                        try {
                            $targetPathWithoutExt = 'news/' . $record->slug . '/' . $record->slug . '-' . date('Y-U');
                            $newPath = \App\Helpers\ImageHelper::processAndConvert($contents, $targetPathWithoutExt);
                            
                            if ($newPath) {
                                $record->update(['thumbnail' => $newPath]);
                                \Illuminate\Support\Facades\Log::info("NewsImporter successfully processed image from {$sourceType} for News ID {$record->id}");
                            } else {
                                throw new \Exception("Gagal mengonversi gambar ke WebP (cek log ImageHelper)");
                            }
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::warning("News Image Processing failed: " . $e->getMessage());
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'thumbnail' => "Gagal memproses gambar: " . $e->getMessage(),
                            ]);
                        }
                    } else {
                        // If both failed, we log it but maybe we should throw to let user know?
                        \Illuminate\Support\Facades\Log::warning("News Image NOT found for News ID {$record->id}. URL and Local ({$thumbnailPath}) both failed.");
                        
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'thumbnail' => "Gambar tidak ditemukan. Pastikan URL benar atau upload file ke 'storage/app/public/import-news/' dengan nama yang sama.",
                        ]);
                    }
                }
            }

            // 4. Sync SEO Metadata
            $seoKeys = !empty($data['seo_keywords']) ? array_map('trim', explode(',', $data['seo_keywords'])) : null;
            $seoData = [
                'title' => Str::limit($data['seo_title'] ?? $record->title, 500, ''),
                'description' => Str::limit($data['seo_description'] ?? Str::limit(strip_tags($record->content), 160, ''), 1000, ''),
                'keywords' => $seoKeys,
                'og_title' => Str::limit($data['og_title'] ?? null, 500, ''),
                'og_description' => Str::limit($data['og_description'] ?? null, 1000, ''),
                'og_image' => \App\Helpers\ImageHelper::resolveImageFromUrl($data['og_image'] ?? null, 'seo/og', $record->slug, $record->thumbnail),
                'canonical_url' => Str::limit($data['canonical_url'] ?? null, 1000, ''),
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
