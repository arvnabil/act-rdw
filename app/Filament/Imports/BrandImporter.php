<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
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
            ImportColumn::make('logo_path')
                ->label('Logo Path')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('brands/logo.png'),
            ImportColumn::make('website_url')
                ->label('Website URL')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('https://brand.com'),
            ImportColumn::make('image')
                ->label('Featured Image')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('brands/featured.jpg'),
            ImportColumn::make('desc')
                ->label('Description')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state),
            ImportColumn::make('category')
                ->label('Category')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('Technology'),
            ImportColumn::make('is_featured')
                ->label('Is Featured')
                ->boolean()
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
                ->rules(['nullable', 'max:60']),
            ImportColumn::make('seo_description')
                ->label('SEO Description')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:160']),
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
        // Default is_featured if blank
        if (blank($this->data['is_featured'] ?? null)) {
            $this->record->is_featured = false;
        }
    }

    protected function afterSave(): void
    {
        \Log::info('Importing Brand ID: ' . $this->record->id . ' | Slug: ' . $this->record->slug);

        // Update Landing Config (Hero)
        $landingConfig = $this->record->landing_config ?? [];
        $hero = $landingConfig['hero'] ?? [];

        // Manual mapping from CSV to Hero Config
        if (isset($this->data['hero_eyebrow'])) $hero['eyebrow'] = $this->data['hero_eyebrow'];
        if (isset($this->data['hero_title'])) $hero['title'] = $this->data['hero_title'];
        if (isset($this->data['hero_subtitle'])) $hero['subtitle'] = $this->data['hero_subtitle'];
        if (isset($this->data['hero_desc'])) $hero['desc'] = $this->data['hero_desc'];
        if (isset($this->data['hero_cta_label'])) $hero['cta_label'] = $this->data['hero_cta_label'];
        if (isset($this->data['hero_cta_url'])) $hero['cta_url'] = $this->data['hero_cta_url'];
        
        // Ensure enabled is true if any hero data is provided
        if (!empty(array_filter($hero))) {
            $hero['enabled'] = $hero['enabled'] ?? true;
        }

        $landingConfig['hero'] = $hero;
        $this->record->landing_config = $landingConfig;
        $this->record->save();

        // SEO Update
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
