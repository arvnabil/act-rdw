<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\Services\Models\Service;
use Modules\Services\Models\ServiceSolution;

Route::middleware(['web'])->group(function () {
    // Service Index Page
    // Service Index Page
    Route::get('/services', function () {
        $slug = 'services';
        $page = \Modules\CMS\Models\Page::where('slug', $slug)->where('status', 'published')->firstOrFail();

        $page->load(['sections' => function ($query) {
            $query->where('is_active', true)->orderBy('position');
        }]);

        $sectionDataResolver = app(\Modules\CMS\Services\SectionDataResolver::class);
        $sections = $page->sections->map(function ($section) use ($sectionDataResolver) {
            return $sectionDataResolver->resolve($section);
        });

        return Inertia::render('Services/Index', [
            'page' => $page,
            'sections' => $sections,
            'seo' => \Modules\SEO\Services\SeoResolver::for($page),
        ]);
    })->name('services.index');

    // Service Detail Page (e.g., /services/video-conference)
    Route::get('/services/{service:slug}', [\Modules\Services\Http\Controllers\ServiceController::class, 'show'])
        ->name('services.detail');

    // Service Item Detail Page (e.g., /services/video-conference/huddle-room)
    Route::get('/services/{service:slug}/{solution:slug}', function (Service $service, ServiceSolution $solution) {
        $solution->load(['brands', 'service']);

        // Dummy Projects for Work Showcase (as requested)
        $dummyProjects = [
            ['title' => 'Reference Project 1', 'category' => 'Enterprise', 'image' => '/assets/img/project/project_3_9_.jpg'],
            ['title' => 'Reference Project 2', 'category' => 'SMB', 'image' => '/assets/img/project/project_3_9_.jpg']
        ];

        return Inertia::render('Services/ItemDetail', [
            'item' => [
                'id' => $solution->id,
                'slug' => $solution->slug,
                'title' => $solution->title,
                'parent_service' => $service->slug,
                'parent_title' => $service->name,
                'entity_type' => 'service_solution',
                'subtitle' => $solution->subtitle,
                'description' => $solution->description,
                'features' => $solution->features ?? [],
                'images' => [
                    $solution->thumbnail,
                    $solution->thumbnail, // Single thumbnail used twice as discussed
                ],
                'brands' => $solution->brands->map(fn ($b) => [
                    'name' => $b->name,
                    'slug' => $b->slug,
                    'image' => $b->image,
                    'thumbnail' => $b->thumbnail,
                    'desc' => $b->short_desc
                ]),
                'projects' => $dummyProjects,
                'configurator_route' => $solution->configurator_slug ? '/' . $solution->configurator_slug : '/room-configurator',
                'thumbnail' => $solution->thumbnail,
                'breadcrumb_image' => $solution->breadcrumb_image,
                'show_breadcrumb' => $solution->show_breadcrumb ?? true,
                'show_showcase' => $solution->show_showcase,
                'wa_message' => $solution->wa_message
            ],
            'seo' => \Modules\SEO\Services\SeoResolver::for($solution)
        ]);
    })->name('services.item.detail');
});
