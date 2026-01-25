<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\SeoResolver;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('status', 'published')
            ->latest('published_at')
            ->paginate(12)
            ->through(function ($project) {
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'slug' => $project->slug,
                    'link' => route('dynamic.resolve', $project->slug),
                    'image' => $project->thumbnail ? "/storage/" . $project->thumbnail : null,
                    'subtitle' => 'Project',
                    'category' => 'Technology',
                ];
            });

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'seo' => SeoResolver::staticPage('Projects', 'Explore our latest projects and case studies.'),
        ]);
    }
}
