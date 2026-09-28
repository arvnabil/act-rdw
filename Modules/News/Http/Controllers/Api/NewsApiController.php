<?php

namespace Modules\News\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\News\Services\NewsQueryService;
use Modules\News\Services\NewsMutationService;
use Modules\News\Transformers\NewsResource;

/**
 * REST API controller for News resources.
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
class NewsApiController extends Controller
{
    public function __construct(
        private readonly NewsQueryService    $queryService,
        private readonly NewsMutationService $mutationService,
    ) {}

    /**
     * Display a listing of the news.
     *
     * GET /api/news
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $news = $this->queryService->list($request->all());

        return NewsResource::collection($news);
    }

    /**
     * Display the specified news.
     *
     * GET /api/news/{slug}
     *
     * @param  string  $slug
     * @return NewsResource|\Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        $news = $this->queryService->findBySlug($slug);

        if (!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }

        return new NewsResource($news);
    }

    /**
     * Store or update news via API (Import).
     *
     * POST /api/news/import
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug'  => 'required|string|max:255',
        ]);

        try {
            $news = $this->mutationService->upsert($request->all());

            return response()->json([
                'message' => 'News imported successfully',
                'news'    => new NewsResource($news),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Import failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
