<?php

namespace Modules\ProductCatalog\Services;

use App\Helpers\ImageHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\ProductCatalog\Models\Brand;
use Modules\ProductCatalog\Models\Product;
use Modules\ProductCatalog\Models\ProductCategory;
use Modules\Services\Models\Service;
use Modules\Services\Models\ServiceSolution;

/**
 * Application service for ProductCatalog mutation operations.
 *
 * Encapsulates the full product upsert (import) workflow extracted from
 * ProductApiController. Shared between the REST import endpoint and the
 * future MCP tool layer.
 */
class ProductMutationService
{
    /**
     * Upsert (create or update) a product from the given payload.
     *
     * This is a full transactional operation covering:
     *   - Brand upsert
     *   - Service upsert
     *   - Image processing (URL download / local path resolution)
     *   - M2M category sync
     *   - M2M solutions sync
     *   - SEO metadata upsert
     *   - Brand-to-solutions sync (if supported by the model)
     *
     * Returns the loaded Product model on success.
     *
     * @param  array<string,mixed>  $data  Validated payload
     * @return Product
     *
     * @throws \Throwable
     */
    public function upsert(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::firstOrNew(['slug' => $data['slug']]);

            // Map direct scalar attributes
            $product->name               = $data['name'];
            $product->sku                = $data['sku'] ?? null;
            $product->price              = isset($data['price']) ? (float) $data['price'] : null;
            $product->description        = $data['description'] ?? null;
            $product->datasheet_url      = $data['datasheet_url'] ?? null;
            $product->specs              = is_string($data['specs'] ?? null)
                ? json_decode($data['specs'], true) : ($data['specs'] ?? null);
            $product->features           = is_string($data['features'] ?? null)
                ? json_decode($data['features'], true) : ($data['features'] ?? null);
            $product->tags               = is_string($data['tags'] ?? null)
                ? array_map('trim', explode(',', $data['tags'])) : ($data['tags'] ?? null);
            $product->specification_text = $data['specification_text'] ?? null;
            $product->features_text      = $data['features_text'] ?? null;
            $product->link_accommerce    = $data['link_accommerce'] ?? null;
            $product->whatsapp_note      = $data['whatsapp_note'] ?? null;
            $product->is_active          = $data['is_active'] ?? true;
            $product->is_featured        = $data['is_featured'] ?? false;

            // Handle Brand upsert
            if (!empty($data['brand_name'])) {
                $brandName = trim($data['brand_name']);
                $brand = Brand::firstOrCreate(
                    ['slug' => Str::slug($brandName)],
                    ['name' => $brandName]
                );
                $product->brand_id = $brand->id;
            }

            // Handle Service upsert
            if (!empty($data['service_name'])) {
                $serviceName = trim($data['service_name']);
                $service = Service::firstOrCreate(
                    ['slug' => Str::slug($serviceName)],
                    ['name' => $serviceName]
                );
                $product->service_id = $service->id;
            }

            // Handle Image processing
            if (!empty($data['image_path'])) {
                $imagePath = trim($data['image_path']);
                if (strtoupper($imagePath) === 'DELETE') {
                    $product->image_path = null;
                } elseif (str_starts_with($imagePath, 'http')) {
                    $localPath = ImageHelper::getLocalPathFromUrl($imagePath);
                    if ($localPath) {
                        $product->image_path = $localPath;
                    } else {
                        try {
                            $cleanUrl = str_replace(' ', '%20', $imagePath);
                            $response = Http::withoutVerifying()->timeout(30)->get($cleanUrl);
                            if ($response->successful()) {
                                $targetPath = 'products/' . $product->slug . '/' . $product->slug . '-' . time();
                                $newPath = ImageHelper::processAndConvert($response->body(), $targetPath);
                                if ($newPath) {
                                    $product->image_path = $newPath;
                                }
                            }
                        } catch (\Throwable $e) {
                            Log::warning('ProductMutationService: image download failed: ' . $e->getMessage());
                        }
                    }
                }
            }

            $product->save();

            // Sync M2M Categories
            if (!empty($data['category_name'])) {
                $catNames = is_array($data['category_name'])
                    ? $data['category_name']
                    : array_map('trim', explode(',', $data['category_name']));
                $catIds = [];
                foreach ($catNames as $catName) {
                    if (empty($catName)) {
                        continue;
                    }
                    $cat = ProductCategory::firstOrCreate(
                        ['slug' => Str::slug($catName)],
                        ['name' => $catName, 'is_active' => true]
                    );
                    $catIds[] = $cat->id;
                }
                $product->categories()->sync($catIds);
            }

            // Sync M2M Solutions
            if (!empty($data['solutions'])) {
                $solutionNames = is_array($data['solutions'])
                    ? $data['solutions']
                    : array_map('trim', explode(',', $data['solutions']));
                $solutionIds = ServiceSolution::whereIn('title', $solutionNames)->pluck('id')->toArray();
                $product->solutions()->sync($solutionIds);
            }

            // Upsert SEO metadata
            $seoKeys = !empty($data['seo_keywords'])
                ? (is_array($data['seo_keywords'])
                    ? $data['seo_keywords']
                    : array_map('trim', explode(',', $data['seo_keywords'])))
                : null;

            $seoData = [
                'title'         => Str::limit($data['seo_title'] ?? $product->name, 500, ''),
                'description'   => Str::limit(
                    $data['seo_description'] ?? Str::limit(strip_tags($product->description), 160, ''),
                    1000, ''
                ),
                'keywords'      => $seoKeys,
                'og_title'      => Str::limit($data['og_title'] ?? null, 500, ''),
                'og_description'=> Str::limit($data['og_description'] ?? null, 1000, ''),
                'og_image'      => ImageHelper::resolveImageFromUrl(
                    $data['og_image'] ?? null,
                    'seo/og',
                    $product->slug,
                    $product->seo?->og_image ?: $product->image_path
                ),
                'canonical_url' => Str::limit($data['canonical_url'] ?? null, 1000, ''),
                'noindex'       => (bool) ($data['noindex'] ?? false),
            ];

            $product->seo()->updateOrCreate(
                ['seoable_id' => $product->id, 'seoable_type' => get_class($product)],
                $seoData
            );

            // Sync brand to associated solutions if model supports it
            if (method_exists($product, 'syncBrandToSolutions')) {
                $product->syncBrandToSolutions();
            }

            return $product->load(['brand', 'service', 'categories', 'solutions', 'seo']);
        });
    }

    public function delete(string $slug): bool
    {
        $product = Product::where('slug', $slug)->first();
        if (!$product) return false;
        return $product->delete();
    }
}
