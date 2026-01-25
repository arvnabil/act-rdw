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
            // Fetch recent posts
            $recentPosts = News::where('status', 'published')
                ->where('id', '!=', $news->id)
                ->latest('published_at')
                ->take(3)
                ->get()
                ->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'date' => $post->published_at ? $post->published_at->format('d M, Y') : '',
                        'image' => $post->featured_image,
                        'link' => route('dynamic.resolve', $post->slug)
                    ];
                });

            // Fetch all categories for sidebar
            $categories = \App\Models\NewsCategory::has('posts')->get()->map(function($cat) {
                 return $cat->name;
            });

            // Fetch popular tags (or just strictly associated tags for now, but request said sidebar has tags)
            // For now let's pass all tags used in sidebar
            $popularTags = \App\Models\NewsTag::has('posts')->limit(10)->get()->map(function($t) {
                return $t->name;
            });

            return Inertia::render('News/Detail', [
                'post' => $news,
                'recentPosts' => $recentPosts,
                'categories' => $categories,
                'tags' => $popularTags, // Sidebar tags
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
