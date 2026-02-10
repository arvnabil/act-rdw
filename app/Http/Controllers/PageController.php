<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\SectionDataResolver;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    protected $resolver;

    public function __construct(SectionDataResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function resolveHomepage()
    {
        $homepage = Page::where('is_homepage', true)
            ->where('status', 'published')
            ->with(['sections' => function ($query) {
                $query->where('is_active', true)->orderBy('position');
            }, 'seo'])
            ->first();

        if (!$homepage) {
            // Fallback to static Home with legacy data
            $services = [];
            if (class_exists(\Modules\ServiceSolutions\Models\Service::class)) {
                $services = \Modules\ServiceSolutions\Models\Service::orderBy('sort_order', 'asc')->take(3)->get();
            }

            $projects = [];
            if (class_exists(\App\Models\Project::class)) {
                $projects = \App\Models\Project::where('status', 'published')
                    ->latest() // Match Builder sorting (created_at)
                    ->limit(8)
                    ->get()
                    ->map(function ($p) {
                         return [
                            'title' => $p->title,
                            'subtitle' => $p->title,
                            'image' => $p->thumbnail ? "/storage/{$p->thumbnail}" : null,
                            'link' => "/projects/{$p->slug}",
                            'category' => $p->is_featured ? 'Featured' : 'Project'
                        ];
                    });
            }

            $clients = [];
            if (class_exists(\App\Models\Client::class)) {
                $clients = \App\Models\Client::where('is_active', true)
                    ->orderBy('position', 'asc')
                    ->get()
                    ->map(function ($c) {
                        return [
                            'name' => $c->name,
                            'image' => $c->logo ? "/storage/{$c->logo}" : null,
                        ];
                    });
            }

            return Inertia::render('Home', [
                'services' => $services,
                'projects' => $projects,
                'clients' => $clients,
                'seo' => \App\Services\SeoResolver::staticPage('Home')
            ]);
        }

        $sectionDataResolver = app(SectionDataResolver::class);
        $sections = $homepage->sections->map(function ($section) use ($sectionDataResolver) {
            return $sectionDataResolver->resolve($section);
        });



        return Inertia::render('DynamicPage', [
            'page' => [
                'id' => $homepage->id,
                'title' => $homepage->title,
                'slug' => $homepage->slug,
                'show_breadcrumb' => $homepage->show_breadcrumb,
            ],
            'sections' => $sections,
            'seo' => \App\Services\SeoResolver::for($homepage),
        ]);
    }

    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('status', 'published')
            ->with(['sections' => function ($query) {
                // Eager load only active sections
                $query->where('is_active', true)->orderBy('position');
            }, 'seo'])
            ->firstOrFail();

        // 1. Prepare Inertia props
        $sectionDataResolver = app(SectionDataResolver::class);
        $sections = $page->sections->map(function ($section) use ($sectionDataResolver) {
            return $sectionDataResolver->resolve($section);
        });

        // 2. Render View
        // DEBUG: Check resolved data for contact section
        // dd($sections->toArray());

        return Inertia::render('DynamicPage', [
            'page' => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'show_breadcrumb' => $page->show_breadcrumb,
            ],
            'sections' => $sections,
            'seo' => \App\Services\SeoResolver::for($page),
        ]);
    }
}
