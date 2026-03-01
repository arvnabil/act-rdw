<?php

namespace Modules\Clients\Http\Controllers;

use Modules\Clients\Models\Client;
use Modules\CMS\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->input('category');

        $clientsQuery = Client::where('is_active', true);

        if ($category && $category !== 'All Clients') {
            $clientsQuery->where(function($q) use ($category) {
                $q->where('category', $category)
                  ->orWhere('category', 'LIKE', '%"'.$category.'"%');
            });
        }

        $clients = $clientsQuery->orderBy('position')
            ->paginate(15)
            ->through(function($c) {
                $resolvePath = function($path) {
                    if (!$path) return null;
                    if (str_starts_with($path, 'http')) return $path;
                    if (str_starts_with($path, 'assets') || str_starts_with($path, '/assets')) {
                        return str_starts_with($path, '/') ? $path : "/{$path}";
                    }
                    return "/storage/{$path}";
                };

                $cats = $c->category;
                if ($cats) {
                    $decoded = json_decode($cats, true);
                    if (is_array($decoded)) {
                        $cats = $decoded;
                    } else {
                        $cats = [$cats];
                    }
                } else {
                    $cats = ['General'];
                }

                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'image' => $resolvePath($c->logo),
                    'website_url' => $c->website_url,
                    'website_rel' => \Modules\SEO\Helpers\SeoHelper::get_rel($c->website_url),
                    'categories' => $cats,
                    'is_featured' => true
                ];
            });

        // Get categories count from ALL active clients for the sidebar
        $allActiveClients = Client::where('is_active', true)->get();
        $allMappedCats = [];
        foreach ($allActiveClients as $client) {
            $cats = $client->category;
            if ($cats) {
                $decoded = json_decode($cats, true);
                $catsArray = is_array($decoded) ? $decoded : [$cats];
            } else {
                $catsArray = ['General'];
            }

            foreach ($catsArray as $catName) {
                if (!isset($allMappedCats[$catName])) {
                    $allMappedCats[$catName] = 0;
                }
                $allMappedCats[$catName]++;
            }
        }

        $categories = collect($allMappedCats)->map(function ($count, $name) {
            return [
                'name' => $name,
                'count' => $count,
            ];
        })->values();

        $page = Page::where('slug', 'clients')->first();
        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'categories' => $categories,
            'filters' => [
                'category' => $category
            ],
            'page_title' => $page?->title ?? 'Our Clients',
            'breadcrumb_image' => $page?->breadcrumb_image,
            'show_breadcrumb' => $page?->show_breadcrumb ?? true,
            'seo' => \Modules\SEO\Services\SeoResolver::staticPage('Our Clients', 'Our trusted clients across various industries.')
        ]);
    }
}
