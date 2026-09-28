<?php

namespace Modules\ProductCatalog\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ProductCatalog\Services\ProductQueryService;
use Modules\ProductCatalog\Services\ProductMutationService;
use Modules\ProductCatalog\Transformers\ProductResource;

/**
 * REST API controller for ProductCatalog resources.
 *
 * This controller is a thin HTTP adapter — it handles request validation,
 * HTTP response formatting, and delegates all domain logic to the injected
 * application services. This allows the same services to be consumed by
 * the MCP tool layer without duplication.
 *
 * HTTP CONTRACT: All endpoints, HTTP methods, URL paths, authentication,
 * request schemas, response structures, and status codes are preserved
 * exactly as before the service-extraction refactor.
 */
class ProductApiController extends Controller
{
    public function __construct(
        private readonly ProductQueryService    $queryService,
        private readonly ProductMutationService $mutationService,
    ) {}

    /**
     * Display a listing of the products.
     *
     * GET /api/products
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $products = $this->queryService->list($request->all());

        return ProductResource::collection($products);
    }

    /**
     * Display the specified product.
     *
     * GET /api/products/{slug}
     *
     * @param  string  $slug
     * @return ProductResource|\Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        $product = $this->queryService->findBySlug($slug);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return new ProductResource($product);
    }

    /**
     * Store or update a product via API (Import).
     *
     * POST /api/products/import
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'slug'         => 'required|string|max:255',
            'brand_name'   => 'required|string|max:255',
            'service_name' => 'required|string|max:255',
        ]);

        try {
            $product = $this->mutationService->upsert($request->all());

            return response()->json([
                'message' => 'Product imported successfully',
                'product' => new ProductResource($product),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Import failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
