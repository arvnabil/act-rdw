<?php

namespace App\Filament\Imports;

use App\Models\Project;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ProjectImporter extends Importer
{
    protected static ?string $model = Project::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Project Alpha'),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('project-alpha'),
            ImportColumn::make('client')
                ->label('Client Name')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('PT Telkom Indonesia'),
            ImportColumn::make('category')
                ->label('Category')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('IT Services'),
            ImportColumn::make('project_date')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->rules(['date', 'nullable'])
                ->example('2024-01-01'),
            ImportColumn::make('address')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('Jalan Gatot Subroto No. 52'),
            ImportColumn::make('excerpt')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state),
            ImportColumn::make('content')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state),
            ImportColumn::make('status')
                ->rules(['in:draft,published'])
                ->example('published'),
            ImportColumn::make('is_featured')
                ->boolean()
                ->example(false),
            ImportColumn::make('thumbnail')
                ->label('Thumbnail (URL or path)')
                ->fillRecordUsing(fn() => null),
            // SEO Columns
            ImportColumn::make('seo_title')
                ->label('SEO Title')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:60'])
                ->example('Project Alpha - Best IT Service'),
            ImportColumn::make('seo_description')
                ->label('SEO Description')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:160']),
            ImportColumn::make('seo_keywords')
                ->label('SEO Keywords')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->helperText('Comma separated keywords'),
            ImportColumn::make('og_title')
                ->label('OG Title')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('og_description')
                ->label('OG Description')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('og_image')
                ->label('OG Image')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null),
            ImportColumn::make('canonical_url')
                ->label('Canonical URL')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('noindex')
                ->label('No Index')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->example(false),
        ];
    }

    public function resolveRecord(): Project
    {
        return Project::firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    protected function beforeSave(): void
    {
        // Default Status if blank
        if (blank($this->data['status'] ?? null)) {
            $this->record->status = 'draft';
        }
        
        // Default is_featured if blank
        if (blank($this->data['is_featured'] ?? null)) {
            $this->record->is_featured = false;
        }
    }

    protected function afterSave(): void
    {
        \Log::info('Importing Project ID: ' . $this->record->id . ' | Slug: ' . $this->record->slug);
        
        $seoKeywords = $this->data['seo_keywords'] ?? null;
        $seoKeywords = blank($seoKeywords) ? null : array_map('trim', explode(',', $seoKeywords));

        $seoData = [
            'title' => blank($this->data['seo_title'] ?? null) ? null : $this->data['seo_title'],
            'description' => blank($this->data['seo_description'] ?? null) ? null : $this->data['seo_description'],
            'keywords' => $seoKeywords,
            'og_title' => blank($this->data['og_title'] ?? null) ? null : $this->data['og_title'],
            'og_description' => blank($this->data['og_description'] ?? null) ? null : $this->data['og_description'],
            'og_image' => blank($this->data['og_image'] ?? null) ? null : $this->data['og_image'],
            'canonical_url' => blank($this->data['canonical_url'] ?? null) ? null : $this->data['canonical_url'],
            'noindex' => blank($this->data['noindex'] ?? null) ? false : (bool) $this->data['noindex'],
        ];

        $this->record->seo()->updateOrCreate(
            ['seoable_id' => $this->record->id, 'seoable_type' => get_class($this->record)],
            $seoData
        );

        // Handle Thumbnail Auto-Download from URL or Local Fallback
        if (isset($this->data['thumbnail'])) {
            $thumbnailPath = trim($this->data['thumbnail']);
            
            if (empty($thumbnailPath) || strtoupper($thumbnailPath) === 'DELETE') {
                $this->record->update(['thumbnail' => null]);
            } else {
                $contents = null;
                $sourceType = 'unknown';

                // Level 1: Check if it's a URL
                if (str_starts_with($thumbnailPath, 'http')) {
                    // Check if it's already a local URL
                    $localPath = \App\Helpers\ImageHelper::getLocalPathFromUrl($thumbnailPath);
                    if ($localPath) {
                        \Log::info("ProjectImporter: Detected local URL '{$thumbnailPath}', skipping download and using existing file: {$localPath}");
                        $this->record->update(['thumbnail' => $localPath]);
                        $contents = null; // Skip further processing
                    } else {
                        \Log::info("ProjectImporter: Detected external URL '{$thumbnailPath}', starting migration to local storage.");
                        try {
                            // Robust URL handling: encode spaces
                            $cleanUrl = str_replace(' ', '%20', $thumbnailPath);
                            
                            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                                ->withHeaders([
                                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
                                    'Accept' => 'image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8'
                                ])
                                ->timeout(60)
                                ->retry(3, 1000)
                                ->get($cleanUrl);

                            \Log::info("ProjectImporter: Download response status: " . $response->status());

                            if ($response->successful()) {
                                $contents = $response->body();
                                $sourceType = 'URL';
                                \Log::info("ProjectImporter: Download successful, size: " . strlen($contents) . " bytes");
                            } else {
                                \Log::warning("ProjectImporter: URL download failed for {$thumbnailPath}. Status: " . $response->status());
                            }
                        } catch (\Throwable $e) {
                             \Log::warning("ProjectImporter: URL download failed for {$thumbnailPath}. Error: " . $e->getMessage());
                        }
                    }
                }

                if ($contents) {
                    try {
                        $targetPathWithoutExt = 'projects/' . $this->record->slug . '/' . $this->record->slug . '-' . time();
                        $newPath = \App\Helpers\ImageHelper::processAndConvert($contents, $targetPathWithoutExt);
                        
                        if ($newPath) {
                            $this->record->update(['thumbnail' => $newPath]);
                        }
                    } catch (\Throwable $e) {
                        \Log::warning("Project Image Processing failed: " . $e->getMessage());
                    }
                }
            }
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Proses import data project selesai. ' . Number::format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' baris gagal diimport.';
        }

        return $body;
    }
}
