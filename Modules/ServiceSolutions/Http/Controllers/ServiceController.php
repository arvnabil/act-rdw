<?php

namespace Modules\ServiceSolutions\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Modules\ServiceSolutions\Models\Service;
use App\Services\SeoResolver;

class ServiceController extends Controller
{
    public function show(Service $service)
    {
        // Load relationships needed for the view
        $service->load(['categories', 'solutions.categories']);

        // Prepare SEO
        // If the service model uses HasSeoMeta, we can use SeoResolver
        // Or manually construct it if SeoResolver isn't adapted for Models yet.
        // Assuming SeoResolver::for($model) works if dynamic logic is in place.
        // If not, we fall back to manual SEO injection.

        // Resolve SEO using the shared resolver if available for arbitrary models
        // But SeoResolver::for($page) expects a Page model usually.
        // Let's inspect SeoResolver later. For now, we will construct SEO array manually
        // using the model's seo relation or defaults.

        return Inertia::render('ServiceDetail', [
            'service' => [
                'id' => $service->slug, // Frontend historically uses slug as id in some places
                'title' => $service->name,
                'hero_subtitle' => $service->hero_subtitle,
                'grid_title' => $service->grid_title,
                'content' => $service->content, 
                'excerpt' => $service->excerpt, 
                'featured_image' => $service->featured_image, 
                'thumbnail' => $service->thumbnail,
                'filters' => $service->categories->map(fn ($cat) => [
                    'label' => $cat->label,
                    'value' => $cat->value
                ]),
                'rooms' => $service->solutions->map(fn ($sol) => [
                    'id' => $sol->slug,
                    'title' => $sol->title,
                    'description' => $sol->description,
                    'image' => $sol->thumbnail,
                    'category' => $sol->categories->pluck('value')->join(' ')
                ])
            ],
            'seo' => SeoResolver::for($service)
        ]);
    }
}
