<?php

namespace Modules\ProductCatalog\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ProductCatalog\Models\Product;
use Modules\ProductCatalog\Models\Brand;
use Modules\ProductCatalog\Models\ProductCategory;
use Modules\Services\Models\Service;
use Modules\Services\Models\ServiceSolution;
use Modules\ProductCatalog\Transformers\ProductResource;
use Illuminate\Support\Str;

class ProductApiController extends Controller
{
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'service', 'categories', 'solutions', 'seo'])
            ->where('is_active', true);

        // Filter by category slug
        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by brand slug
        if ($request->has('brand')) {
             $query->whereHas('brand', function ($q) use ($request) {
                $q->where('slug', $request->brand);
            });
        }

        $products = $query->latest()->paginate($request->get('limit', 15));

        return ProductResource::collection($products);
    }

    /**
     * Display the specified product.
     *
     * @param string $slug
     * @return ProductResource|\Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        $product = Product::with(['brand', 'service', 'categories', 'solutions', 'seo'])
            ->where('slug', $slug)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return new ProductResource($product);
    }

    /**
     * Store or update a product via API (Import).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'brand_name' => 'required|string|max:255',
            'service_name' => 'required|string|max:255',
        ]);

        $data = $request->all();

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $product = Product::firstOrNew(['slug' => $data['slug']]);

            // Map direct attributes
            $product->name = $data['name'];
            $product->sku = $data['sku'] ?? null;
            $product->price = isset($data['price']) ? (float) $data['price'] : null;
            $product->description = $data['description'] ?? null;
            $product->datasheet_url = $data['datasheet_url'] ?? null;
            $product->specs = is_string($data['specs'] ?? null) ? json_decode($data['specs'], true) : ($data['specs'] ?? null);
            $product->features = is_string($data['features'] ?? null) ? json_decode($data['features'], true) : ($data['features'] ?? null);
            $product->tags = is_string($data['tags'] ?? null) ? array_map('trim', explode(',', $data['tags'])) : ($data['tags'] ?? null);
            $product->specification_text = $data['specification_text'] ?? null;
            $product->features_text = $data['features_text'] ?? null;
            $product->link_accommerce = $data['link_accommerce'] ?? null;
            $product->whatsapp_note = $data['whatsapp_note'] ?? null;
            $product->is_active = $data['is_active'] ?? true;
            $product->is_featured = $data['is_featured'] ?? false;

            // Handle Brand
            if (!empty($data['brand_name'])) {
                $brandName = trim($data['brand_name']);
                $brand = Brand::firstOrCreate(
                    ['slug' => Str::slug($brandName)],
                    ['name' => $brandName]
                );
                $product->brand_id = $brand->id;
            }

            // Handle Service
            if (!empty($data['service_name'])) {
                $serviceName = trim($data['service_name']);
                $service = Service::firstOrCreate(
                    ['slug' => Str::slug($serviceName)],
                    ['name' => $serviceName]
                );
                $product->service_id = $service->id;
            }

            // Handle Image Processing
            if (!empty($data['image_path'])) {
                $imagePath = trim($data['image_path']);
                if (strtoupper($imagePath) === 'DELETE') {
                    $product->image_path = null;
                } elseif (str_starts_with($imagePath, 'http')) {
                    $localPath = \App\Helpers\ImageHelper::getLocalPathFromUrl($imagePath);
                    if ($localPath) {
                        $product->image_path = $localPath;
                    } else {
                        try {
                            $cleanUrl = str_replace(' ', '%20', $imagePath);
                            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                                ->timeout(30)->get($cleanUrl);
                            
                            if ($response->successful()) {
                                $contents = $response->body();
                                $targetPathWithoutExt = 'products/' . $product->slug . '/' . $product->slug . '-' . time();
                                $newPath = \App\Helpers\ImageHelper::processAndConvert($contents, $targetPathWithoutExt);
                                if ($newPath) {
                                    $product->image_path = $newPath;
                                }
                            }
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::warning("Product API Import Image Failed: " . $e->getMessage());
                        }
                    }
                }
            }

            $product->save();

            // M2M Categories
            if (!empty($data['category_name'])) {
                $catNames = is_array($data['category_name']) ? $data['category_name'] : array_map('trim', explode(',', $data['category_name']));
                $catIds = [];
                foreach ($catNames as $catName) {
                    if (empty($catName)) continue;
                    $cat = ProductCategory::firstOrCreate(
                        ['slug' => Str::slug($catName)],
                        ['name' => $catName, 'is_active' => true]
                    );
                    $catIds[] = $cat->id;
                }
                $product->categories()->sync($catIds);
            }

            // M2M Solutions
            if (!empty($data['solutions'])) {
                $solutionNames = is_array($data['solutions']) ? $data['solutions'] : array_map('trim', explode(',', $data['solutions']));
                $solutionIds = ServiceSolution::whereIn('title', $solutionNames)->pluck('id')->toArray();
                $product->solutions()->sync($solutionIds);
            }

            // SEO Metadata
            $seoKeys = !empty($data['seo_keywords']) ? 
                (is_array($data['seo_keywords']) ? $data['seo_keywords'] : array_map('trim', explode(',', $data['seo_keywords']))) : null;
            
            $seoData = [
                'title' => Str::limit($data['seo_title'] ?? $product->name, 500, ''),
                'description' => Str::limit($data['seo_description'] ?? Str::limit(strip_tags($product->description), 160, ''), 1000, ''),
                'keywords' => $seoKeys,
                'og_title' => Str::limit($data['og_title'] ?? null, 500, ''),
                'og_description' => Str::limit($data['og_description'] ?? null, 1000, ''),
                'og_image' => \App\Helpers\ImageHelper::resolveImageFromUrl($data['og_image'] ?? null, 'seo/og', $product->slug, $product->seo?->og_image ?: $product->image_path),
                'canonical_url' => Str::limit($data['canonical_url'] ?? null, 1000, ''),
                'noindex' => (bool) ($data['noindex'] ?? false),
            ];

            $product->seo()->updateOrCreate(
                ['seoable_id' => $product->id, 'seoable_type' => get_class($product)],
                $seoData
            );

            // Sync Brand to Solutions inside Product model
            if (method_exists($product, 'syncBrandToSolutions')) {
                $product->syncBrandToSolutions();
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'message' => 'Product imported successfully',
                'product' => new ProductResource($product->load(['brand', 'service', 'categories', 'solutions', 'seo']))
            ], 200);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['error' => 'Import failed', 'message' => $e->getMessage()], 500);
        }
    }
}
