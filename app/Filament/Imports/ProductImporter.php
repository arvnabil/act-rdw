<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Modules\Core\Models\Product;
use Modules\Core\Models\Brand;
use Modules\Core\Models\ProductCategory;
use Modules\ServiceSolutions\Models\Service;
use Illuminate\Support\Str;
use App\Models\SeoMeta;
use Modules\ServiceSolutions\Models\ServiceSolution;

class ProductImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            // Basic Info
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Yealink MVC840'),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('yealink-mvc840'),
            ImportColumn::make('sku')
                ->label('SKU')
                ->rules(['nullable', 'max:255'])
                ->example('MVC840-C2-211'),
            ImportColumn::make('price')
                ->numeric()
                ->rules(['nullable', 'numeric'])
                ->example('25000000'),
            ImportColumn::make('description')
                ->label('Description')
                ->example('Microsoft Teams Room System for Large Rooms'),
            ImportColumn::make('image_path')
                ->label('Image Path')
                ->example('products/yealink-mvc840.jpg'),
            ImportColumn::make('datasheet_url')
                ->label('Datasheet URL')
                ->rules(['nullable', 'url'])
                ->example('https://example.com/datasheet.pdf'),
            
            // Relationships (Auto-Create if not exists)
            // Relationships (Auto-Create if not exists)
            ImportColumn::make('brand_name')
                ->label('Brand')
                ->rules(['required', 'string', 'max:255'])
                ->fillRecordUsing(function ($record, $state) {
                    $state = trim($state);
                    $slug = Str::slug($state);
                    $item = Brand::firstOrCreate(
                        ['slug' => $slug],
                        ['name' => $state]
                    );
                    $record->brand()->associate($item);
                })
                ->example('Yealink'),
            ImportColumn::make('category_name')
                ->label('Category')
                ->rules(['required', 'string', 'max:255'])
                ->fillRecordUsing(function ($record, $state) {
                    $state = trim($state);
                    $slug = Str::slug($state);
                    $item = ProductCategory::firstOrCreate(
                        ['slug' => $slug],
                        ['name' => $state, 'is_active' => true]
                    );
                    $record->category()->associate($item);
                })
                ->example('Video Conferencing'),
            ImportColumn::make('service_name')
                ->label('Service')
                ->rules(['required', 'string', 'max:255'])
                ->fillRecordUsing(function ($record, $state) {
                    $state = trim($state);
                    $slug = Str::slug($state);
                    $item = Service::firstOrCreate(
                        ['slug' => $slug],
                        ['name' => $state]
                    );
                    $record->service()->associate($item);
                })
                ->example('Communication'),
            
            // M2M Relationship (Comma Separated)
            // M2M Relationship (Comma Separated)
            ImportColumn::make('solutions')
                ->label('Solutions (Separate by comma)')
                ->fillRecordUsing(function ($record) {
                    // Explicitly unset if it was accidentally set
                    if (isset($record->solutions)) {
                        unset($record->solutions);
                    }
                })
                ->example('Microsoft Teams, Zoom Rooms'),
            
            // Market Links
            ImportColumn::make('link_accommerce')
                ->label('Acommerce Link')
                ->rules(['nullable', 'url']),
            ImportColumn::make('whatsapp_note')
                ->label('WhatsApp Note'),

            // Status
            ImportColumn::make('is_active')
                ->boolean()
                ->fillRecordUsing(fn ($record, $state) => $record->is_active = $state ?? true)
                ->example('yes'),
            ImportColumn::make('is_featured')
                ->boolean()
                ->fillRecordUsing(fn ($record, $state) => $record->is_featured = $state ?? false)
                ->example('no'),

            // SEO Data (Manual Handling)
            ImportColumn::make('seo_title')
                ->label('SEO Title')
                ->rules(['nullable', 'max:255'])
                ->fillRecordUsing(fn ($record, $state) => null) 
                ->castStateUsing(fn ($state) => blank($state) ? null : $state),
            ImportColumn::make('seo_description')
                ->label('SEO Description')
                ->rules(['nullable', 'max:500'])
                ->fillRecordUsing(fn ($record, $state) => null)
                ->castStateUsing(fn ($state) => blank($state) ? null : $state),
            ImportColumn::make('seo_keywords')
                ->label('SEO Keywords')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->castStateUsing(fn ($state) => blank($state) ? null : $state),
            ImportColumn::make('og_title')
                ->label('OG Title')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->castStateUsing(fn ($state) => blank($state) ? null : $state),
            ImportColumn::make('og_description')
                ->label('OG Description')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->castStateUsing(fn ($state) => blank($state) ? null : $state),
            ImportColumn::make('og_image')
                ->label('OG Image')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->castStateUsing(fn ($state) => blank($state) ? null : $state),
            ImportColumn::make('canonical_url')
                ->label('Canonical URL')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->castStateUsing(fn ($state) => blank($state) ? null : $state),
            ImportColumn::make('noindex')
                ->boolean()
                ->label('No Index')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->castStateUsing(fn ($state) => blank($state) ? false : (bool) $state),
        ];
    }

    public function resolveRecord(): Product
    {
        return Product::firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $data = $this->data;

        // 1. Handle M2M Solutions
        if (!empty($data['solutions'])) {
            $solutionNames = array_map('trim', explode(',', $data['solutions']));
            $solutionIds = ServiceSolution::whereIn('title', $solutionNames)->pluck('id')->toArray();
            $record->solutions()->sync($solutionIds);
        }

        // 2. Handle SEO Metadata
        $seoData = [
            'title' => $data['seo_title'] ?? $record->name,
            'description' => $data['seo_description'] ?? Str::limit(strip_tags($record->description), 160),
            'keywords' => $data['seo_keywords'] ?? null,
            'og_title' => $data['og_title'] ?? null,
            'og_description' => $data['og_description'] ?? null,
            'og_image' => $data['og_image'] ?? $record->image_path,
            'canonical_url' => $data['canonical_url'] ?? null,
            'noindex' => $data['noindex'] ?? false,
        ];

        // Remove nulls to avoid overwriting existing data with nulls if partial update
        // But for import, usually we want to set what's provided. 
        // Logic: specific SEO columns provided > default fallback.
        
        $record->seo()->updateOrCreate(
            ['seoable_id' => $record->id, 'seoable_type' => get_class($record)],
            $seoData
        );
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $successCount = Number::format($import->successful_rows);
        $failedCount = Number::format($import->getFailedRowsCount());

        $body = "Import Produk selesai! {$successCount} baris berhasil diproses.";

        if ($failedCount > 0) {
            $body .= " Ada {$failedCount} baris yang gagal.";
        }

        return $body;
    }
}
