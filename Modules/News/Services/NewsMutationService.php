<?php

namespace Modules\News\Services;

use App\Helpers\ImageHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\News\Models\News;
use Modules\News\Models\NewsCategory;
use Modules\News\Models\NewsTag;

/**
 * Application service for News mutation operations.
 *
 * Encapsulates the full news upsert (import) workflow extracted from
 * NewsApiController. Shared between the REST import endpoint and the
 * future MCP tool layer.
 */
class NewsMutationService
{
    /**
     * Upsert (create or update) a news article from the given payload.
     *
     * This is a full transactional operation covering:
     *   - Direct attribute mapping
     *   - Image / thumbnail processing
     *   - M2M category sync
     *   - M2M tag sync
     *   - SEO metadata upsert
     *
     * Returns the loaded News model on success.
     *
     * @param  array<string,mixed>  $data  Validated payload
     * @return News
     *
     * @throws \Throwable
     */
    public function upsert(array $data): News
    {
        return DB::transaction(function () use ($data) {
            $news = News::firstOrNew(['slug' => $data['slug']]);

            // Map direct attributes
            $news->title     = $data['title'];
            $news->excerpt   = $data['excerpt'] ?? null;
            $news->content   = $data['content'] ?? null;
            $news->status    = $data['status'] ?? 'draft';

            if (!empty($data['published_at'])) {
                $news->published_at = Carbon::parse($data['published_at']);
            }

            // Handle thumbnail image processing
            if (!empty($data['thumbnail'])) {
                $thumbnailPath = trim($data['thumbnail']);
                if (strtoupper($thumbnailPath) === 'DELETE') {
                    $news->thumbnail = null;
                } elseif (str_starts_with($thumbnailPath, 'http')) {
                    $localPath = ImageHelper::getLocalPathFromUrl($thumbnailPath);
                    if ($localPath) {
                        $news->thumbnail = $localPath;
                    } else {
                        try {
                            $cleanUrl = str_replace(' ', '%20', $thumbnailPath);
                            $response = Http::withoutVerifying()->timeout(30)->get($cleanUrl);
                            if ($response->successful()) {
                                $targetPath = 'news/' . $news->slug . '/' . $news->slug . '-' . time();
                                $newPath = ImageHelper::processAndConvert($response->body(), $targetPath);
                                if ($newPath) {
                                    $news->thumbnail = $newPath;
                                }
                            }
                        } catch (\Throwable $e) {
                            Log::warning('NewsMutationService: thumbnail download failed: ' . $e->getMessage());
                        }
                    }
                }
            }

            $news->save();

            // Sync M2M Categories
            if (!empty($data['categories'])) {
                $catNames = is_array($data['categories'])
                    ? $data['categories']
                    : array_map('trim', explode(',', $data['categories']));
                $catIds = [];
                foreach ($catNames as $catName) {
                    if (empty($catName)) {
                        continue;
                    }
                    $cat = NewsCategory::firstOrCreate(
                        ['slug' => Str::slug($catName)],
                        ['name' => $catName]
                    );
                    $catIds[] = $cat->id;
                }
                $news->categories()->sync($catIds);
            }

            // Sync M2M Tags
            if (!empty($data['tags'])) {
                $tagNames = is_array($data['tags'])
                    ? $data['tags']
                    : array_map('trim', explode(',', $data['tags']));
                $tagIds = [];
                foreach ($tagNames as $tagName) {
                    if (empty($tagName)) {
                        continue;
                    }
                    $tag = NewsTag::firstOrCreate(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName]
                    );
                    $tagIds[] = $tag->id;
                }
                $news->tags()->sync($tagIds);
            }

            // Upsert SEO metadata
            $seoKeys = !empty($data['seo_keywords'])
                ? (is_array($data['seo_keywords'])
                    ? $data['seo_keywords']
                    : array_map('trim', explode(',', $data['seo_keywords'])))
                : null;

            $seoData = [
                'title'         => Str::limit($data['seo_title'] ?? $news->title, 500, ''),
                'description'   => Str::limit(
                    $data['seo_description'] ?? Str::limit(strip_tags($news->content), 160, ''),
                    1000, ''
                ),
                'keywords'      => $seoKeys,
                'og_title'      => Str::limit($data['og_title'] ?? null, 500, ''),
                'og_description'=> Str::limit($data['og_description'] ?? null, 1000, ''),
                'og_image'      => ImageHelper::resolveImageFromUrl(
                    $data['og_image'] ?? null,
                    'seo/og',
                    $news->slug,
                    $news->seo?->og_image ?: $news->thumbnail
                ),
                'canonical_url' => Str::limit($data['canonical_url'] ?? null, 1000, ''),
                'noindex'       => (bool) ($data['noindex'] ?? false),
            ];

            $news->seo()->updateOrCreate(
                ['seoable_id' => $news->id, 'seoable_type' => get_class($news)],
                $seoData
            );

            return $news->load(['categories', 'tags', 'seo']);
        });
    }

    public function delete(string $slug): bool
    {
        $news = News::where('slug', $slug)->first();
        if (!$news) return false;
        return $news->delete();
    }
}
