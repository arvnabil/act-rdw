<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Models\Brand;

class BrandImporter extends Importer
{
    protected static ?string $model = Brand::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Brand Name'),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('brand-slug'),
            ImportColumn::make('image')
                ->label('Logo')
                ->fillRecordUsing(fn() => null)
                ->example('https://example.com/logo.png'),
            ImportColumn::make('website_url')
                ->label('Website URL')
                ->fillRecordUsing(fn() => null)
                ->example('https://brand.com'),
            ImportColumn::make('desc')
                ->label('Description')
                ->fillRecordUsing(fn() => null),
            ImportColumn::make('category')
                ->label('Category')
                ->fillRecordUsing(fn() => null)
                ->example('Technology, Security'),
            ImportColumn::make('is_featured')
                ->label('Is Featured')
                ->fillRecordUsing(fn() => null)
                ->example(false),
            
            // Hero Configuration (Virtual Columns)
            ImportColumn::make('hero_eyebrow')
                ->label('Hero Eyebrow')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('AUTHORIZED PARTNER'),
            ImportColumn::make('hero_title')
                ->label('Hero Title')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('Best Solution for Your Business'),
            ImportColumn::make('hero_subtitle')
                ->label('Hero Subtitle')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('Business Solution'),
            ImportColumn::make('hero_desc')
                ->label('Hero Description')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->castStateUsing(fn ($state) => blank($state) ? null : $state),
            ImportColumn::make('hero_cta_label')
                ->label('Hero CTA Label')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('Contact Sales'),
            ImportColumn::make('hero_cta_url')
                ->label('Hero CTA URL')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('#products'),

            // SEO Columns
            ImportColumn::make('seo_title')
                ->label('SEO Title')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('seo_description')
                ->label('SEO Description')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:500']),
            ImportColumn::make('seo_keywords')
                ->label('SEO Keywords')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null),
            ImportColumn::make('og_title')
                ->label('OG Title')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null),
            ImportColumn::make('og_description')
                ->label('OG Description')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null),
            ImportColumn::make('og_image')
                ->label('OG Image')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null),
            ImportColumn::make('canonical_url')
                ->label('Canonical URL')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null),
            ImportColumn::make('noindex')
                ->label('No Index')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->example(false),
        ];
    }

    public function resolveRecord(): Brand
    {
        return Brand::firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    protected function beforeSave(): void
    {
        $isNew = !$this->record->exists;

        // For text fields: only update if CSV value is not blank
        // If blank, preserve existing DB value (for updates) or leave null (for new records)
        $textFields = ['website_url', 'desc', 'category'];
        foreach ($textFields as $field) {
            $csvValue = $this->data[$field] ?? null;
            if (!blank($csvValue)) {
                $this->record->{$field} = $csvValue;
            }
            // If blank: don't touch existing value on update, leave null on create
        }

        // is_featured: only update if provided, default false for new records
        $csvFeatured = $this->data['is_featured'] ?? null;
        if (!blank($csvFeatured)) {
            $this->record->is_featured = filter_var($csvFeatured, FILTER_VALIDATE_BOOLEAN);
        } elseif ($isNew) {
            $this->record->is_featured = false;
        }
    }

    protected function afterSave(): void
    {
        \Log::info('Importing Brand ID: ' . $this->record->id . ' | Slug: ' . $this->record->slug);

        // Update Landing Config (Hero) - only update fields that have data
        $landingConfig = $this->record->landing_config ?? [];
        $hero = $landingConfig['hero'] ?? [];

        $heroFields = [
            'hero_eyebrow' => 'eyebrow',
            'hero_title' => 'title',
            'hero_subtitle' => 'subtitle',
            'hero_desc' => 'desc',
            'hero_cta_label' => 'cta_label',
            'hero_cta_url' => 'cta_url',
        ];

        foreach ($heroFields as $csvKey => $heroKey) {
            if (!blank($this->data[$csvKey] ?? null)) {
                $hero[$heroKey] = $this->data[$csvKey];
            }
        }
        
        // Ensure enabled is true if any hero data is provided
        if (!empty(array_filter($hero))) {
            $hero['enabled'] = $hero['enabled'] ?? true;
        }

        $landingConfig['hero'] = $hero;
        $this->record->landing_config = $landingConfig;
        $this->record->save();

        // SEO Update - only update non-blank fields, preserve existing for blank ones
        $existingSeo = $this->record->seo;

        $seoKeywords = $this->data['seo_keywords'] ?? null;
        $seoKeywords = blank($seoKeywords) ? null : array_map('trim', explode(',', $seoKeywords));

        $seoData = [];
        $seoFieldMap = [
            'seo_title' => 'title',
            'seo_description' => 'description',
            'og_title' => 'og_title',
            'og_description' => 'og_description',
            'og_image' => 'og_image',
            'canonical_url' => 'canonical_url',
        ];

        foreach ($seoFieldMap as $csvKey => $dbKey) {
            $csvValue = $this->data[$csvKey] ?? null;
            if (!blank($csvValue)) {
                $seoData[$dbKey] = $csvValue;
            } elseif ($existingSeo) {
                // Preserve existing value
                $seoData[$dbKey] = $existingSeo->{$dbKey};
            } else {
                $seoData[$dbKey] = null;
            }
        }

        // Keywords: special handling (array)
        if (!blank($seoKeywords)) {
            $seoData['keywords'] = $seoKeywords;
        } elseif ($existingSeo) {
            $seoData['keywords'] = $existingSeo->keywords;
        } else {
            $seoData['keywords'] = null;
        }

        // Noindex
        $csvNoindex = $this->data['noindex'] ?? null;
        if (!blank($csvNoindex)) {
            $seoData['noindex'] = filter_var($csvNoindex, FILTER_VALIDATE_BOOLEAN);
        } elseif ($existingSeo) {
            $seoData['noindex'] = $existingSeo->noindex;
        } else {
            $seoData['noindex'] = false;
        }

        $this->record->seo()->updateOrCreate(
            ['seoable_id' => $this->record->id, 'seoable_type' => get_class($this->record)],
            $seoData
        );

        // Handle image/logo
        $logo = $this->data['image'] ?? null;
        \Log::info('BrandImporter: afterSave for Record: ' . $this->record->id);
        \Log::info('BrandImporter: Logo value from CSV: ' . ($logo ?: 'NULL'));

        // If image field is blank in CSV, skip entirely (preserve existing)
        if (blank($logo)) {
            \Log::info('BrandImporter: Image field is blank, preserving existing image.');
            return;
        }

        // Check if it's a URL
        if (filter_var($logo, FILTER_VALIDATE_URL) || str_starts_with($logo, 'http')) {
            // 1. Check if it's already a local URL pointing to our storage
            $localPath = \App\Helpers\ImageHelper::getLocalPathFromUrl($logo);
            
            if ($localPath) {
                // If local path is same as current image, skip
                if ($this->record->image === $localPath) {
                    \Log::info('BrandImporter: Image unchanged, skipping. Path: ' . $localPath);
                    return;
                }
                \Log::info("BrandImporter: Local URL detected, using: {$localPath}");
                $this->record->update(['image' => $localPath]);
            } else {
                // 2. Check if existing image URL matches the CSV URL (no re-download needed)
                $existingImageUrl = $this->record->image
                    ? asset(Storage::url($this->record->image))
                    : null;

                if ($existingImageUrl && $existingImageUrl === $logo) {
                    \Log::info('BrandImporter: External URL matches existing image, skipping download.');
                    return;
                }

                // 3. Different URL - download and replace
                \Log::info("BrandImporter: New external URL, downloading: {$logo}");
                try {
                    $cleanUrl = str_replace(' ', '%20', $logo);
                    
                    $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                            'Accept' => 'image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8'
                        ])
                        ->timeout(60)
                        ->retry(3, 1000)
                        ->get($cleanUrl);

                    if ($response->successful()) {
                        $contents = $response->body();
                        \Log::info('BrandImporter: Downloaded ' . strlen($contents) . ' bytes');
                        
                        // Delete old image if exists
                        if ($this->record->image && Storage::disk('public')->exists($this->record->image)) {
                            Storage::disk('public')->delete($this->record->image);
                            \Log::info('BrandImporter: Deleted old image: ' . $this->record->image);
                        }

                        $filename = \Illuminate\Support\Str::slug($this->record->name ?: 'brand') . '-' . time();
                        $targetPath = 'brands/' . $filename;
                        
                        $savedPath = \App\Helpers\ImageHelper::processAndConvert($contents, $targetPath);
                        if ($savedPath) {
                            \Log::info('BrandImporter: New image saved: ' . $savedPath);
                            $this->record->update(['image' => $savedPath]);
                        } else {
                            \Log::error('BrandImporter: ImageHelper returned NULL after processing.');
                        }
                    } else {
                        \Log::warning('BrandImporter: Download failed. Status: ' . $response->status());
                    }
                } catch (\Throwable $e) {
                    \Log::error('BrandImporter: Failed to download logo: ' . $e->getMessage());
                }
            }
        } else {
            // Non-URL value (e.g. local path like "brands/logo.webp")
            // Only update if different from existing
            if ($this->record->image !== $logo) {
                \Log::info('BrandImporter: Updating image path to: ' . $logo);
                $this->record->update(['image' => $logo]);
            } else {
                \Log::info('BrandImporter: Image path unchanged, skipping.');
            }
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Proses import data brand selesai. ' . Number::format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' baris gagal diimport.';
        }

        return $body;
    }
}
