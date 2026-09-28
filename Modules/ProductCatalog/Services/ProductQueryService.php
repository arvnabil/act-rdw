<?php

namespace Modules\ProductCatalog\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\ProductCatalog\Models\Product;

/**
 * Application service for ProductCatalog read operations.
 *
 * Encapsulates query logic shared between the REST API and the MCP tool layer.
 * Controllers and MCP tools both delegate here so ORM calls are not duplicated
 * across entry points.
 */
class ProductQueryService
{
    /**
     * Return a paginated, filtered collection of active products.
     *
     * Supported filters:
     *   - category  (string) slug
     *   - brand     (string) slug
     *   - limit     (int)    items per page, default 15
     *
     * @param  array<string,mixed>  $filters
     * @return LengthAwarePaginator
     */
    public function list(array $filters): LengthAwarePaginator
    {
        $query = Product::with(['brand', 'service', 'categories', 'solutions', 'seo'])
            ->where('is_active', true);

        if (!empty($filters['category'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        if (!empty($filters['brand'])) {
            $query->whereHas('brand', function ($q) use ($filters) {
                $q->where('slug', $filters['brand']);
            });
        }

        $limit = (int) ($filters['limit'] ?? 15);

        return $query->latest()->paginate($limit);
    }

    /**
     * Find a single product by slug, or return null if not found.
     *
     * @param  string  $slug
     * @return Product|null
     */
    public function findBySlug(string $slug): ?Product
    {
        return Product::with(['brand', 'service', 'categories', 'solutions', 'seo'])
            ->where('slug', $slug)
            ->first();
    }
}
