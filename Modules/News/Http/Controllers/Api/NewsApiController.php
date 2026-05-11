<?php

namespace Modules\News\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\News\Models\News;
use Modules\News\Models\NewsCategory;
use Modules\News\Models\NewsTag;
use Modules\News\Transformers\NewsResource;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NewsApiController extends Controller
{
    /**
     * Display a listing of the news.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = News::with(['categories', 'tags', 'author', 'seo'])
            ->where('status', 'published');

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by tag
        if ($request->has('tag')) {
             $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        $news = $query->latest('published_at')->paginate($request->get('limit', 15));

        return NewsResource::collection($news);
    }

    /**
     * Display the specified news.
     *
     * @param string $slug
     * @return NewsResource|\Illuminate\Http\JsonResponse
     */
    public function show($slug)
    {
        $news = News::with(['categories', 'tags', 'author', 'seo'])
            ->where('slug', $slug)
            ->first();

        if (!$news) {
            return response()->json(['message' => 'News not found'], 404);
        }

        return new NewsResource($news);
    }

    /**
     * Store or update news via API (Import).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
        ]);

        $data = $request->all();

        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            $news = News::firstOrNew(['slug' => $data['slug']]);

            // Map direct attributes
            $news->title = $data['title'];
            $news->excerpt = $data['excerpt'] ?? null;
            $news->content = $data['content'] ?? null;
            $news->status = $data['status'] ?? 'draft';
            
            if (!empty($data['published_at'])) {
                $news->published_at = Carbon::parse($data['published_at']);
            }

            // Handle Image Processing
            if (!empty($data['thumbnail'])) {
                $thumbnailPath = trim($data['thumbnail']);
                if (strtoupper($thumbnailPath) === 'DELETE') {
                    $news->thumbnail = null;
                } elseif (str_starts_with($thumbnailPath, 'http')) {
                    $localPath = \App\Helpers\ImageHelper::getLocalPathFromUrl($thumbnailPath);
                    if ($localPath) {
                        $news->thumbnail = $localPath;
                    } else {
                        try {
                            $cleanUrl = str_replace(' ', '%20', $thumbnailPath);
                            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                                ->timeout(30)->get($cleanUrl);
                            
                            if ($response->successful()) {
                                $contents = $response->body();
                                $targetPathWithoutExt = 'news/' . $news->slug . '/' . $news->slug . '-' . time();
                                $newPath = \App\Helpers\ImageHelper::processAndConvert($contents, $targetPathWithoutExt);
                                if ($newPath) {
                                    $news->thumbnail = $newPath;
                                }
                            }
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::warning("News API Import Image Failed: " . $e->getMessage());
                        }
                    }
                }
            }

            $news->save();

            // M2M Categories
            if (!empty($data['categories'])) {
                $catNames = is_array($data['categories']) ? $data['categories'] : array_map('trim', explode(',', $data['categories']));
                $catIds = [];
                foreach ($catNames as $catName) {
                    if (empty($catName)) continue;
                    $cat = NewsCategory::firstOrCreate(
                        ['slug' => Str::slug($catName)],
                        ['name' => $catName]
                    );
                    $catIds[] = $cat->id;
                }
                $news->categories()->sync($catIds);
            }

            // M2M Tags
            if (!empty($data['tags'])) {
                $tagNames = is_array($data['tags']) ? $data['tags'] : array_map('trim', explode(',', $data['tags']));
                $tagIds = [];
                foreach ($tagNames as $tagName) {
                    if (empty($tagName)) continue;
                    $tag = NewsTag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName]
                    );
                    $tagIds[] = $tag->id;
                }
                $news->tags()->sync($tagIds);
            }

            // SEO Metadata
            $seoKeys = !empty($data['seo_keywords']) ? 
                (is_array($data['seo_keywords']) ? $data['seo_keywords'] : array_map('trim', explode(',', $data['seo_keywords']))) : null;
            
            $seoData = [
                'title' => Str::limit($data['seo_title'] ?? $news->title, 500, ''),
                'description' => Str::limit($data['seo_description'] ?? Str::limit(strip_tags($news->content), 160, ''), 1000, ''),
                'keywords' => $seoKeys,
                'og_title' => Str::limit($data['og_title'] ?? null, 500, ''),
                'og_description' => Str::limit($data['og_description'] ?? null, 1000, ''),
                'og_image' => \App\Helpers\ImageHelper::resolveImageFromUrl($data['og_image'] ?? null, 'seo/og', $news->slug, $news->seo?->og_image ?: $news->thumbnail),
                'canonical_url' => Str::limit($data['canonical_url'] ?? null, 1000, ''),
                'noindex' => (bool) ($data['noindex'] ?? false),
            ];

            $news->seo()->updateOrCreate(
                ['seoable_id' => $news->id, 'seoable_type' => get_class($news)],
                $seoData
            );

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'message' => 'News imported successfully',
                'news' => new NewsResource($news->load(['categories', 'tags', 'seo']))
            ], 200);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['error' => 'Import failed', 'message' => $e->getMessage()], 500);
        }
    }
}
