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
                ->fillRecordUsing(fn ($record, $state) => $record->specs = \App\Helpers\JsonHelper::nest($state))
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
                ->label('Category (Comma Separated)')
                // Logic moved to afterSave to support multiple
                ->fillRecordUsing(function ($record) {
                    // Do nothing here, allow afterSave to handle
                })
                ->example('Video Conferencing, Audio Device'),
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
        // Fix: Remove product_category_id if it exists in data (legacy mapping) to prevent SQL error
        // Use unset directly to handle null values (isset returns false on null)
        unset($this->data['product_category_id']);

        return Product::firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $data = $this->data;

        // 0. Handle M2M Categories (New)
        if (!empty($data['category_name'])) {
            $catNames = array_map('trim', explode(',', $data['category_name']));
            $catIds = [];
            foreach ($catNames as $catName) {
                if (empty($catName)) continue;
                $slug = Str::slug($catName);
                $cat = ProductCategory::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $catName, 'is_active' => true]
                );
                $catIds[] = $cat->id;
            }
            $record->categories()->sync($catIds);
        }

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

        // 2. Handle Thumbnail Auto-Download from URL or Local Fallback
        if (isset($data['image_path'])) {
            $thumbnailPath = trim($data['image_path']);
            
            if (empty($thumbnailPath) || strtoupper($thumbnailPath) === 'DELETE') {
                $record->update(['image_path' => null]);
            } else {
                $contents = null;
                $sourceType = 'unknown';

                // Level 1: Check if it's a URL
                if (str_starts_with($thumbnailPath, 'http')) {
                    // Check if it's already a local URL
                    $localPath = \App\Helpers\ImageHelper::getLocalPathFromUrl($thumbnailPath);
                    if ($localPath) {
                        \Illuminate\Support\Facades\Log::info("ProductImporter: Detected local URL '{$thumbnailPath}', skipping download and using existing file: {$localPath}");
                        $record->update(['image_path' => $localPath]);
                        $contents = null; // Skip further processing
                    } else {
                        try {
                            \Illuminate\Support\Facades\Log::info("ProductImporter: Detected external URL '{$thumbnailPath}', starting migration to local storage.");
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
                            
                            \Illuminate\Support\Facades\Log::info("ProductImporter: Download response status: " . $response->status());

                            if ($response->successful()) {
                                $contents = $response->body();
                                $sourceType = 'URL';
                                \Illuminate\Support\Facades\Log::info("ProductImporter: Download successful, size: " . strlen($contents) . " bytes");
                            } else {
                                \Illuminate\Support\Facades\Log::warning("ProductImporter: URL download failed for {$thumbnailPath}. Status: " . $response->status());
                            }
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::info("ProductImporter: URL download attempt failed for {$thumbnailPath}. Error: " . $e->getMessage());
                        }
                    }
                }

                // Level 2: Local Fallback (Direct Upload)
                // If URL failed OR if it was just a filename (e.g. "myimage.jpg")
                if (!$contents) {
                    $filename = basename($thumbnailPath);
                    $localPath = 'import-products/' . $filename;
                    
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($localPath)) {
                        $contents = \Illuminate\Support\Facades\Storage::disk('public')->get($localPath);
                        $sourceType = 'Local (import-products/)';
                    }
                }

                if ($contents) {
                    try {
                        $targetPathWithoutExt = 'products/' . $record->slug . '/' . $record->slug . '-' . date('Y-U');
                        $newPath = \App\Helpers\ImageHelper::processAndConvert($contents, $targetPathWithoutExt);
                        
                        if ($newPath) {
                            $record->update(['image_path' => $newPath]);
                            \Illuminate\Support\Facades\Log::info("ProductImporter successfully processed image from {$sourceType} for Product ID {$record->id}");
                        } else {
                            throw new \Exception("Gagal mengonversi gambar ke WebP (cek log ImageHelper)");
                        }
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::warning("Product Image Processing failed: " . $e->getMessage());
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'image_path' => "Gagal memproses gambar: " . $e->getMessage(),
                        ]);
                    }
                } else {
                    \Illuminate\Support\Facades\Log::warning("Product Image NOT found for Product ID {$record->id}. URL and Local ({$thumbnailPath}) both failed.");
                    
                    // Fallback to preserve URL string if it's not fatal, but we throw validation error for visibility
                    $record->update(['image_path' => Str::limit($thumbnailPath, 190)]);

                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'image_path' => "Gambar tidak ditemukan. Pastikan URL benar atau upload file ke 'storage/app/public/import-products/' dengan nama yang sama.",
                    ]);
                }
            }
        }

        // 2b. Handle Datasheet deletion
        if (isset($data['datasheet_url'])) {
            $datasheet = trim($data['datasheet_url']);
            if (empty($datasheet) || strtoupper($datasheet) === 'DELETE') {
                $record->update(['datasheet_url' => null]);
            }
        }

        // 3. Handle SEO Metadata
        $seoKeys = !empty($data['seo_keywords']) ? array_map('trim', explode(',', $data['seo_keywords'])) : null;
        $seoData = [
            'title' => Str::limit($data['seo_title'] ?? $record->name, 500, ''),
            'description' => Str::limit($data['seo_description'] ?? Str::limit(strip_tags($record->description), 160, ''), 1000, ''),
            'keywords' => $seoKeys,
            'og_title' => Str::limit($data['og_title'] ?? null, 500, ''),
            'og_description' => Str::limit($data['og_description'] ?? null, 1000, ''),
            'og_image' => Str::limit($data['og_image'] ?? $record->image_path, 2000, ''),
            'canonical_url' => Str::limit($data['canonical_url'] ?? null, 1000, ''),
            'noindex' => (bool) ($data['noindex'] ?? false),
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

        // 4. Sync Brand to Solutions
        try {
            $record->syncBrandToSolutions();
        } catch (\Throwable $e) {
             \Illuminate\Support\Facades\Log::warning("Brand sync failed for Product {$record->id}: " . $e->getMessage());
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
