<?php

namespace Modules\News\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\News\Models\News;

/**
 * Application service for News read operations.
 *
 * Encapsulates query logic shared between the REST API and the MCP tool layer.
 */
class NewsQueryService
{
    /**
     * Return a paginated, filtered collection of published news articles.
     *
     * Supported filters:
     *   - category  (string) slug
     *   - tag       (string) slug
     *   - limit     (int)    items per page, default 15
     *
     * @param  array<string,mixed>  $filters
     * @return LengthAwarePaginator
     */
    public function list(array $filters): LengthAwarePaginator
    {
        $query = News::with(['categories', 'tags', 'author', 'seo'])
            ->where('status', 'published');

        if (!empty($filters['category'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        if (!empty($filters['tag'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->where('slug', $filters['tag']);
            });
        }

        $limit = (int) ($filters['limit'] ?? 15);

        return $query->latest('published_at')->paginate($limit);
    }

    /**
     * Find a single news article by slug, or return null if not found.
     *
     * @param  string  $slug
     * @return News|null
     */
    public function findBySlug(string $slug): ?News
    {
        return News::with(['categories', 'tags', 'author', 'seo'])
            ->where('slug', $slug)
            ->first();
    }
}
