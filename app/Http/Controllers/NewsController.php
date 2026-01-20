<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Services\SeoResolver;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function index()
    {
        $posts = News::where('status', 'published')
            ->latest('published_at')
            ->paginate(10)
            ->through(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'link' => route('dynamic.resolve', $post->slug),
                    'image' => $post->thumbnail,
                    'date' => $post->published_at ? $post->published_at->format('d M, Y') : '',
                    'author' => 'ACTiV Team',
                    'category' => 'Technology',
                    'excerpt' => $post->excerpt,
                ];
            });

        return Inertia::render('News/Index', [
            'posts' => $posts,
            'seo' => SeoResolver::staticPage('News', 'Latest new and updates from ACTiV'),
        ]);
    }
}
