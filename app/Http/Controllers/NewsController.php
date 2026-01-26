<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Services\SeoResolver;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function index($categorySlug = null, $tagSlug = null)
    {
        $query = News::where('status', 'published')->latest('published_at');

        $activeCategory = null;
        $activeTag = null;
        $pageTitle = 'News';

        if ($request = request()) {
            // Check if we are in a named route context that implies specific filter
            // Or handle parameters passed directly if changing route definition
        }

        // Handle Category Filter
        if ($categorySlug) {
             $activeCategory = \App\Models\NewsCategory::where('slug', $categorySlug)->firstOrFail();
             $query->whereHas('categories', function ($q) use ($activeCategory) {
                 $q->where('id', $activeCategory->id);
             });
             $pageTitle = "News - Category: {$activeCategory->name}";
        }

        // Handle Tag Filter
        if ($tagSlug) {
             $activeTag = \App\Models\NewsTag::where('slug', $tagSlug)->firstOrFail();
             $query->whereHas('tags', function ($q) use ($activeTag) {
                 $q->where('id', $activeTag->id);
             });
             $pageTitle = "News - Tag: {$activeTag->name}";
        }

        $posts = $query->with(['categories', 'tags'])->paginate(10)
            ->through(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'link' => route('dynamic.resolve', $post->slug),
                    'image' => $post->thumbnail,
                    'date' => $post->published_at ? $post->published_at->format('d M, Y') : '',
                    'author' => 'ACTiV Team',
                    'category' => $post->categories->first()?->name ?? 'Uncategorized', // Display primary category
                    'categories' => $post->categories->map(fn($c) => ['name' => $c->name, 'slug' => $c->slug]),
                    'tags' => $post->tags->map(fn($t) => ['name' => $t->name, 'slug' => $t->slug]),
                    'excerpt' => $post->excerpt,
                ];
            });

        // Sidebar Data
        $categories = \App\Models\NewsCategory::withCount('posts')->orderBy('name')->get();
        $tags = \App\Models\NewsTag::orderBy('name')->take(20)->get(); // Limit mostly used tags if many

        return Inertia::render('News/Index', [
            'posts' => $posts,
            'categories' => $categories,
            'tags' => $tags,
            'activeCategory' => $activeCategory,
            'activeTag' => $activeTag,
            'seo' => SeoResolver::staticPage($pageTitle, 'Latest news and updates from ACTiV'),
        ]);
    }

    public function category($slug)
    {
        return $this->index($slug, null);
    }

    public function tag($slug)
    {
        return $this->index(null, $slug);
    }
}
