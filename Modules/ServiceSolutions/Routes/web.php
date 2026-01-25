<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\ServiceSolutions\Models\Service;
use Modules\ServiceSolutions\Models\ServiceSolution;

Route::middleware(['web'])->group(function () {
    // Service Index Page
    // Service Index Page
    Route::get('/services', function () {
        $slug = 'services';
        $page = \App\Models\Page::where('slug', $slug)->where('status', 'published')->firstOrFail();

        $page->load(['sections' => function ($query) {
            $query->where('is_active', true)->orderBy('position');
        }]);

        $sectionDataResolver = app(\App\Services\SectionDataResolver::class);
        $sections = $page->sections->map(function ($section) use ($sectionDataResolver) {
            return $sectionDataResolver->resolve($section);
        });

        return Inertia::render('Services', [
            'page' => $page,
            'sections' => $sections,
            'seo' => \App\Services\SeoResolver::for($page),
        ]);
    })->name('services.index');

    // Service Detail Page (e.g., /services/video-conference)
    Route::get('/services/{service:slug}', function (Service $service) {
        $service->load(['categories', 'solutions.categories']);

        return Inertia::render('ServiceDetail', [
            'service' => [
                'id' => $service->slug,
                'title' => $service->name,
                'hero_subtitle' => $service->hero_subtitle,
                'grid_title' => $service->grid_title,
                'filters' => $service->categories->map(fn ($cat) => [
                    'label' => $cat->label,
                    'value' => $cat->value
                ]),
                'rooms' => $service->solutions->map(fn ($sol) => [
                    'id' => $sol->slug,
                    'title' => $sol->title,
                    'description' => $sol->description,
                    'capacity' => $sol->capacity, // Added if needed later
                    'size' => $sol->size,         // Added if needed later
                    'image' => $sol->thumbnail,
                    'category' => $sol->categories->pluck('value')->join(' ')
                ])
            ]
        ]);
    })->name('services.detail');

    // Service Item Detail Page (e.g., /services/video-conference/huddle-room)
    Route::get('/services/{service:slug}/{solution:slug}', function (Service $service, ServiceSolution $solution) {
        $solution->load(['brands', 'service']);

        // Dummy Projects for Work Showcase (as requested)
        $dummyProjects = [
            ['title' => 'Reference Project 1', 'category' => 'Enterprise', 'image' => '/assets/img/project/project_3_9_.jpg'],
            ['title' => 'Reference Project 2', 'category' => 'SMB', 'image' => '/assets/img/project/project_3_9_.jpg']
        ];

        return Inertia::render('ServiceItemDetail', [
            'item' => [
                'id' => $solution->slug,
                'title' => $solution->title,
                'parent_service' => $service->slug,
                'parent_title' => $service->name,
                'subtitle' => $solution->subtitle,
                'description' => $solution->description,
                'features' => $solution->features ?? [],
                'images' => [
                    $solution->thumbnail,
                    $solution->thumbnail, // Single thumbnail used twice as discussed
                ],
                'brands' => $solution->brands->map(fn ($b) => [
                    'name' => $b->name,
                    'image' => $b->logo_path,
                    'desc' => $b->short_desc
                ]),
                'projects' => $dummyProjects,
                'configurator_route' => $solution->configurator_slug ? '/' . $solution->configurator_slug : '/room-configurator',
                'wa_message' => $solution->wa_message
            ]
        ]);
    })->name('services.item.detail');
});
