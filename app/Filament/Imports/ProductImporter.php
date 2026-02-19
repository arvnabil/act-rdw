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
                ->label('Image Path / URL')
                ->fillRecordUsing(fn ($record, $state) => null) // Handle in afterSave
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('https://example.com/product.jpg'),
            ImportColumn::make('datasheet_url')
                ->label('Datasheet URL')
                ->rules(['nullable', 'url'])
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('https://example.com/datasheet.pdf'),
            
            // Specifications & Features
            ImportColumn::make('specs')
                ->label('Specs (JSON)')
                ->fillRecordUsing(fn ($record, $state) => $record->specs = \App\Helpers\JsonHelper::nest($state, 'Spesifikasi'))
                ->example('{"Dimensi - Tinggi": "100 mm", "Dimensi - Lebar": "50 mm"}'),
            ImportColumn::make('features')
                ->label('Features (JSON)')
                ->fillRecordUsing(fn ($record, $state) => $record->features = is_string($state) ? json_decode($state, true) : $state)
                ->example('[{"name": "Fast", "value": "Yes"}]'),
            ImportColumn::make('tags')
                ->label('Tags (Comma Separated)')
                ->fillRecordUsing(fn ($record, $state) => $record->tags = is_string($state) ? array_map('trim', explode(',', $state)) : $state)
                ->example('Gadget, New, Sale'),
            ImportColumn::make('specification_text')
                ->label('Specification Text (Rich)')
                ->example('<p>Detailed specs...</p>'),
            ImportColumn::make('features_text')
                ->label('Features Text (Rich)')
                ->example('<ul><li>Feature 1</li></ul>'),
            
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
                ->rules(['nullable', 'url'])
                ->castStateUsing(fn ($state) => blank($state) ? null : $state),
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
        try {
            if (!empty($data['solutions'])) {
                $solutionNames = array_map('trim', explode(',', $data['solutions']));
                $solutionIds = ServiceSolution::whereIn('title', $solutionNames)->pluck('id')->toArray();
                $record->solutions()->sync($solutionIds);
            }
        } catch (\Throwable $e) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'solutions' => "Gagal menghubungkan Solusi: " . $e->getMessage(),
            ]);
        }

        // 2. Handle Image Auto-Download from URL
        if ($imageUrl = $data['image_path'] ?? null) {
            $imageUrl = trim($imageUrl);
            if (str_starts_with($imageUrl, 'http')) {
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(15)->get($imageUrl);
                    if ($response->successful()) {
                        $contents = $response->body();
                        $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                        $filename = $record->slug . '-activ-teknologi-' . date('Y-is') . '.' . $extension;
                        $path = 'products/' . $record->slug . '/' . $filename;
                        
                        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $contents);
                        $record->updateQuietly(['image_path' => $path]);
                    } else {
                        throw new \Exception("Status HTTP {$response->status()}");
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning("Product image download failed for {$record->id}: " . $e->getMessage());
                    // Fallback to URL if download fails, but let's notify the user via row error if it was a catastrophic failure
                    // For now, just logging and using fallback to allow completion
                    $record->updateQuietly(['image_path' => Str::limit($imageUrl, 190)]);
                }
            } else if (!empty($imageUrl)) {
                // Limit the path to avoid database errors if URL is too long and wasn't downloaded
                $record->updateQuietly(['image_path' => Str::limit($imageUrl, 190)]);
            }
        }

        // 3. Handle SEO Metadata
        $seoData = [
            'title' => Str::limit($data['seo_title'] ?? $record->name, 190),
            'description' => $data['seo_description'] ?? Str::limit(strip_tags($record->description), 160),
            'keywords' => Str::limit($data['seo_keywords'] ?? null, 190),
            'og_title' => Str::limit($data['og_title'] ?? null, 190),
            'og_description' => $data['og_description'] ?? null, 
            'og_image' => Str::limit($data['og_image'] ?? $record->image_path, 190),
            'canonical_url' => Str::limit($data['canonical_url'] ?? null, 190),
            'noindex' => $data['noindex'] ?? false,
        ];

        \Illuminate\Support\Facades\Log::debug("SEO Data for Product {$record->id}: " . json_encode($seoData, JSON_PRETTY_PRINT));

        try {
            $record->seo()->updateOrCreate(
                ['seoable_id' => $record->id, 'seoable_type' => get_class($record)],
                $seoData
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("SEO Meta sync failed for Product {$record->id}: " . $e->getMessage() . " | SQL values length - og_image: " . strlen($seoData['og_image'] ?? ''));
            throw \Illuminate\Validation\ValidationException::withMessages([
                'seo_title' => "Masalah SEO Meta: " . $e->getMessage(),
            ]);
        }
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
