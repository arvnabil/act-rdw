<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\SeoResolver;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $query = Project::with(['brands', 'solutions'])
            ->where('status', 'published');

        // Search
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('excerpt', 'LIKE', "%{$search}%")
                  ->orWhere('client', 'LIKE', "%{$search}%");
            });
        }

        // Filter by Industry (category)
        if ($industry = request('industry')) {
            $query->where('category', $industry);
        }

        // Filter by Brand (slug or ID)
        if ($brandId = request('brand')) {
            $query->whereHas('brands', function($q) use ($brandId) {
                $q->where('brands.id', $brandId);
            });
        }

        // Filter by Solution
        if ($solutionId = request('solution')) {
            $query->whereHas('solutions', function($q) use ($solutionId) {
                $q->where('service_solutions.id', $solutionId);
            });
        }

        $projects = $query->latest('published_at')
            ->paginate(12)
            ->withQueryString()
            ->through(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'slug' => $project->slug,
                    'link' => route('dynamic.resolve', $project->slug),
                    'image' => $project->thumbnail ? "/storage/" . $project->thumbnail : null,
                    'client' => $project->client,
                    'industry' => $project->category,
                    'year' => $project->project_date ? $project->project_date->format('Y') : null,
                    'excerpt' => \Illuminate\Support\Str::limit($project->excerpt ?? strip_tags($project->content), 120),
                    'brands' => $project->brands->map(fn($b) => ['id' => $b->id, 'name' => $b->name, 'image' => $b->logo_path]),
                    'solutions' => $project->solutions->map(fn($s) => ['id' => $s->id, 'title' => $s->title]),
                    'tags' => $project->tags ?? [],
                ];
            });

        // Options for filters
        $filters = [
            'industries' => ['Education', 'Government', 'Enterprise', 'Finance', 'Healthcare', 'Retails'],
            'brands' => \Modules\Core\Models\Brand::all(['id', 'name']),
            'solutions' => \Modules\ServiceSolutions\Models\ServiceSolution::all(['id', 'title']),
        ];

        // Quick Stats
        $stats = [
            'total_projects' => Project::where('status', 'published')->count(),
            'industries_count' => Project::whereNotNull('category')->distinct('category')->count(),
            'years_experience' => date('Y') - 1999, // Assuming started in 1999 or adjust as needed
        ];

        $page = \App\Models\Page::where('slug', 'projects')->first();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'filters' => $filters,
            'stats' => $stats,
            'page_title' => $page?->title ?? 'Our Projects',
            'breadcrumb_image' => $page?->breadcrumb_image,
            'show_breadcrumb' => $page?->show_breadcrumb ?? true,
            'queryParams' => request()->all(['search', 'industry', 'brand', 'solution']),
            'seo' => SeoResolver::staticPage('Projects', 'Explore our premium ICT solutions and successful project implementations.'),
        ]);
    }
}
