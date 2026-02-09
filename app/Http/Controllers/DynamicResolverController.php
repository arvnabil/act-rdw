<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Page;
use App\Models\Project;
use App\Services\SeoResolver;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Core\Models\Brand;
use Modules\ServiceSolutions\Models\Service;

class DynamicResolverController extends Controller
{
    /**
     * Resolve the slug to a Page, Brand, News, or Project.
     * Priority: Page > Brand > News > Project
     */
    public function resolve(string $slug)
    {
        // 1. Check Pages
        $page = Page::where('slug', $slug)->where('status', 'published')->first();
        if ($page) {
            // Check if it's dynamic page or builder page (assuming logic exists in PageController)
            // For now, we reuse standard Page logic or redirect to PageController if needed
            // But requirement is to handle it here.

            // If it's a "Builder" page, we might need specific props.
            // Ensure sections are loaded
            $page->load(['sections' => function ($query) {
                $query->where('is_active', true)->orderBy('position');
            }]);

            $sectionDataResolver = app(\App\Services\SectionDataResolver::class);
            $sections = $page->sections->map(function ($section) use ($sectionDataResolver) {
                return $sectionDataResolver->resolve($section);
            });

            return Inertia::render('DynamicPage', [
                'page' => [
                    'id' => $page->id,
                    'title' => $page->title,
                    'slug' => $page->slug,
                    'show_breadcrumb' => $page->show_breadcrumb,
                    // Add other necessary fields
                ],
                'sections' => $sections,
                'seo' => SeoResolver::for($page),
            ]);
        }

        // 2. Check Brands
        // We defer to the dedicated BrandController because it has complex logic for
        // categories, related services, and product filtering.
        $brand = Brand::where('slug', $slug)
            ->orWhere('name', 'LIKE', str_replace('-', ' ', $slug))
            ->first();

        if ($brand || Brand::where('name', 'LIKE', $slug)->exists()) {
            return app(\Modules\Core\Http\Controllers\BrandController::class)->show($slug);
        }

        // 3. Check News
        $news = News::with(['categories', 'tags'])->where('slug', $slug)->where('status', 'published')->first();
        if ($news) {
            $resolvePath = function($path) {
                if (!$path) return null;
                if (str_starts_with($path, 'http')) return $path;
                if (str_starts_with($path, 'assets') || str_starts_with($path, '/assets')) {
                    return str_starts_with($path, '/') ? $path : "/{$path}";
                }
                return "/storage/{$path}";
            };

            // Fetch recent posts
            $recentPosts = News::where('status', 'published')
                ->where('id', '!=', $news->id)
                ->latest('published_at')
                ->take(3)
                ->get()
                ->map(function ($post) use ($resolvePath) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'date' => $post->published_at ? $post->published_at->format('d M, Y') : '',
                        'image' => $resolvePath($post->thumbnail),
                        'link' => route('dynamic.resolve', $post->slug)
                    ];
                });

            // Fetch all categories for sidebar
            $categories = \App\Models\NewsCategory::withCount('posts')->orderBy('name')->get();

            // Fetch popular tags
            $tags = \App\Models\NewsTag::orderBy('name')->take(20)->get();

            // Prepare post data with resolved image paths
            $postData = $news->toArray();
            $postData['image'] = $resolvePath($news->thumbnail);
            $postData['thumbnail'] = $postData['image']; // For BlogDetailContent compatibility

            return Inertia::render('News/Detail', [
                'post' => $postData,
                'recentPosts' => $recentPosts,
                'categories' => $categories,
                'tags' => $tags,
                'seo' => SeoResolver::for($news),
            ]);
        }

        // 4. Check Projects
        $project = Project::where('slug', $slug)->where('status', 'published')->first();
        if ($project) {
            // Fix image path for Detail page
            $project->thumbnail = $project->thumbnail ? "/storage/" . $project->thumbnail : null;

            return Inertia::render('Projects/Detail', [
                'project' => $project,
                'seo' => SeoResolver::for($project),
            ]);
        }

        // 5. Check Services (Optional, strictly if requested by user logic, though not in primary list)
        // User didn't strictly list Service in priority, but good to have if needed. Skipping for strict adherence.

        abort(404);
    }
}
