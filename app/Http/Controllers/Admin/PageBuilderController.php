<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PageBuilderController extends Controller
{
    public function edit(Page $page)
    {
        // Ensure we have the latest sections ordered by position
        $page->load(['sections' => function ($query) {
            $query->orderBy('position', 'asc');
        }]);

        // Load global data for builder context (e.g. Services, Projects)
        $allServices = [];
        if (class_exists(\Modules\ServiceSolutions\Models\Service::class)) {
            $allServices = \Modules\ServiceSolutions\Models\Service::orderBy('sort_order', 'asc')->get();
        }

        // Load Projects for builder context
        $allProjects = [];
        if (class_exists(\App\Models\Project::class)) {
            // Map the projects to the format expected by ProjectSection.jsx
            $allProjects = \App\Models\Project::where('status', 'published')
                ->latest('published_at')
                ->limit(8)
                ->get()
                ->map(function ($p) {
                    return [
                        'title' => $p->title,
                        'subtitle' => $p->title,
                        'category' => $p->is_featured ? 'Featured' : 'Project',
                        'image' => $p->thumbnail ? "/storage/{$p->thumbnail}" : null,
                        'link' => "/projects/{$p->slug}",
                    ];
                });
        }

        // Resolve sections with defaults using SectionDataResolver
        $sectionDataResolver = app(\App\Services\SectionDataResolver::class);
        $resolvedSections = $page->sections->map(function ($section) use ($sectionDataResolver) {
            // We need to keep the original Model properties (id, key, active, position)
            // but MERGE the resolved props into 'config'.
            // The Resolver returns ['id', 'section_key', 'props' => [...]]
            // We want the Builder to see the resolved defaults in 'config'.

            $resolved = $sectionDataResolver->resolve($section);
            $props = $resolved['props'];

            // TRANSFORM FOR BUILDER: Adapter for Data Structure Mismatch
            // Filament stores images as ['url1', 'url2'] (Flat Array) via Accessor
            // PageBuilder Inspector expects [{image: 'url1'}, {image: 'url2'}] (Repeater Object)
            if (in_array($section->section_key, ['about', 'about_content', 'why_choose_us'])) {
                if (isset($props['images']) && is_array($props['images'])) {
                    $transformedImages = [];
                    foreach ($props['images'] as $img) {
                        if (is_string($img)) {
                            $transformedImages[] = ['image' => $img];
                        } else {
                            $transformedImages[] = $img;
                        }
                    }
                    $props['images'] = $transformedImages;
                }
            }

            // Allow builder to see resolved defaults by merging them into config
            // Note: 'props' contains the resolved data.
            $section->config = $props;

            return $section;
        });

        return Inertia::render('Admin/PageBuilder/PageBuilder', [
            'page' => $page,
            'sections' => $resolvedSections,
            'allServices' => $allServices,
            'allProjects' => $allProjects,
        ]);
    }

    public function update(Request $request, Page $page)
    {
        \Illuminate\Support\Facades\Log::info("PageBuilder Save Request for Page ID: {$page->id}", [
            'sections_count' => count($request->input('sections') ?? []),
            'payload_size' => strlen(json_encode($request->all()))
        ]);

        $request->validate([
            'sections' => 'required|array',
            'sections.*.section_key' => 'required|string',
            'sections.*.config' => 'nullable|array',
            'sections.*.is_active' => 'boolean',
        ]);

        $sections = $request->input('sections');

        DB::transaction(function () use ($page, $sections) {
            // 1. Identify IDs present in the payload (existing sections)
            $incomingIds = collect($sections)
                ->pluck('id')
                ->filter(function ($id) {
                    return $id && !str_starts_with((string)$id, 'new-');
                })
                ->toArray();

            // 2. Delete sections NOT in the payload (that belong to this page)
            $deleted = PageSection::where('page_id', $page->id)
                ->whereNotIn('id', $incomingIds)
                ->delete();

            \Illuminate\Support\Facades\Log::info("PageBuilder: Deleted {$deleted} old sections for Page {$page->id}");

            // 3. Update or Create sections
            foreach ($sections as $index => $sectionData) {
                $config = $sectionData['config'] ?? [];

                // TRANSFORM FOR DATABASE: Adapter for Data Structure Mismatch
                // PageBuilder sends [{image: 'url1'}, {image: 'url2'}]
                // DB/Filament expects ['url1', 'url2']
                if (in_array($sectionData['section_key'], ['about', 'about_content', 'why_choose_us'])) {
                    if (isset($config['images']) && is_array($config['images'])) {
                        $flatImages = [];
                        foreach ($config['images'] as $img) {
                            if (is_array($img) && isset($img['image'])) {
                                $flatImages[] = $img['image'];
                            } elseif (is_string($img)) {
                                $flatImages[] = $img;
                            }
                        }
                        $config['images'] = $flatImages;
                    }
                }

                // If it has an ID, update it
                if (isset($sectionData['id']) && !str_starts_with((string)$sectionData['id'], 'new-')) {
                    $section = PageSection::find($sectionData['id']);
                    if ($section && $section->page_id === $page->id) {
                        // CLEANUP LOGIC: Check for replaced images
                        $this->cleanupOldImages($section->config, $config);

                        $section->update([
                            'position' => $index,
                            'config' => $config,
                            'is_active' => $sectionData['is_active'] ?? true,
                        ]);
                    }
                } else {
                    // It's a new section
                    $page->sections()->create([
                        'section_key' => $sectionData['section_key'],
                        'position' => $index,
                        'config' => $config,
                        'is_active' => $sectionData['is_active'] ?? true,
                    ]);
                }
            }
        });

        \Illuminate\Support\Facades\Log::info("PageBuilder: Successfully saved Page {$page->id}");

        return back()->with('success', 'Page saved successfully.');
    }

    /**
     * Handle Image Upload
     */
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            // Store in 'page-builder' directory for better organization and safe cleanup
            $path = $file->store('page-builder', 'public');
            return response()->json(['url' => '/storage/' . $path]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    /**
     * Compare old and new config, delete images that are removed.
     */
    private function cleanupOldImages($oldConfig, $newConfig)
    {
        if (!$oldConfig) return;

        $oldImages = $this->extractImages($oldConfig);
        $newImages = $this->extractImages($newConfig);

        // Images present in Old but NOT in New
        $deletedImages = array_diff($oldImages, $newImages);

        foreach ($deletedImages as $imgUrl) {
            $this->deleteImageFile($imgUrl);
        }
    }

    /**
     * Recursively extract all string values that look like our managed images.
     */
    private function extractImages($data)
    {
        $images = [];

        if (is_array($data)) {
            foreach ($data as $value) {
                $images = array_merge($images, $this->extractImages($value));
            }
        } elseif (is_string($data)) {
            // Check if it matches our storage paths (page-builder or legacy uploads)
            if (
                str_starts_with($data, '/storage/page-builder/') ||
                str_starts_with($data, '/storage/uploads/') ||
                str_starts_with($data, '/uploads/')
            ) {
                $images[] = $data;
            }
        }

        return $images;
    }

    /**
     * Delete the physical file if it exists in storage.
     */
    private function deleteImageFile($url)
    {
        // 1. Remove '/storage/' prefix to get relative disk path
        $path = str_replace('/storage/', '', $url);

        // 2. Handle legacy paths that might have started with /uploads/ directly
        if (str_starts_with($path, '/')) {
            $path = substr($path, 1);
        }

        // 3. SAFETY CHECK: Only delete if path starts with allowed folders
        // We DO NOT want to delete /assets/, /seeder/, or root files.
        $allowedFolders = ['page-builder/', 'uploads/'];
        $isSafe = false;
        foreach ($allowedFolders as $folder) {
            if (str_starts_with($path, $folder)) {
                $isSafe = true;
                break;
            }
        }

        if ($isSafe && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        }
    }
}
