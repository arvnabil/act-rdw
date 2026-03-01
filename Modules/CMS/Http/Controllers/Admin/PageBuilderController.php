<?php

namespace Modules\CMS\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\CMS\Models\Page;
use Modules\CMS\Models\PageSection;
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
        if (class_exists(\Modules\Services\Models\Service::class)) {
            $allServices = \Modules\Services\Models\Service::orderBy('sort_order', 'asc')->get();
        }

        // Load Projects for builder context
        $allProjects = [];
        if (class_exists(\Modules\Projects\Models\Project::class)) {
            // Map the projects to the format expected by ProjectSection.jsx
            $allProjects = \Modules\Projects\Models\Project::where('status', 'published')
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
        $sectionDataResolver = app(\Modules\CMS\Services\SectionDataResolver::class);
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

        return Inertia::render('Activioncms/PageBuilder/PageBuilder', [
            'page' => $page,
            'sections' => $resolvedSections,
            'allServices' => $allServices,
            'allProjects' => $allProjects,
        ]);
    }

    public function update(Request $request, Page $page)
    {
        \Illuminate\Support\Facades\Log::info("PageBuilder START Save for Page ID: {$page->id}");

        $request->validate([
            'sections' => 'required|array',
            'sections.*.section_key' => 'required|string',
            // Config can be array (legacy/local) or string (JSON stringified to avoid max_input_vars)
            'sections.*.config' => 'nullable',
            'sections.*.is_active' => 'boolean',
            
            // Page level settings
            'status' => 'nullable|string|in:draft,published',
            'show_breadcrumb' => 'nullable|boolean',
            'breadcrumb_image' => 'nullable|string',
        ]);

        $sections = $request->input('sections');

        \Illuminate\Support\Facades\Log::info("PageBuilder: Payload received", [
            'sections_count' => count($sections),
            'raw_ids' => collect($sections)->pluck('id')->toArray()
        ]);

        DB::transaction(function () use ($page, $sections, $request) {
            // Update Page level settings if provided
            $pageData = [];
            if ($request->has('status')) $pageData['status'] = $request->status;
            if ($request->has('show_breadcrumb')) $pageData['show_breadcrumb'] = $request->show_breadcrumb;
            if ($request->has('breadcrumb_image')) {
                // Ensure we remove /storage/ prefix before saving to DB if it's there
                $path = $request->breadcrumb_image;
                $path = str_replace('/storage/', '', $path);
                $pageData['breadcrumb_image'] = $path;
            }

            if (!empty($pageData)) {
                $page->update($pageData);
            }
            // 1. Identify IDs present in the payload (existing sections)
            // Filter out nulls and 'new-' prefix
            $incomingIds = collect($sections)
                ->pluck('id')
                ->filter(function ($id) {
                    return !empty($id) && !str_starts_with((string)$id, 'new-');
                })
                ->values() // RESET KEYS to ensure toArray() returns a pure list, not associative with gaps
                ->toArray();

            \Illuminate\Support\Facades\Log::info("PageBuilder: IDs to keep", ['ids' => $incomingIds]);

            // 2. Delete sections NOT in the payload (that belong to this page)
            $deleted = PageSection::where('page_id', $page->id)
                ->whereNotIn('id', $incomingIds)
                ->delete();

            \Illuminate\Support\Facades\Log::info("PageBuilder: Deleted {$deleted} old sections");

            // 3. Update or Create sections
            foreach ($sections as $index => $sectionData) {
                // Decode config if it's a string (which it should be now)
                $configInput = $sectionData['config'] ?? [];
                if (is_string($configInput)) {
                    $config = json_decode($configInput, true) ?? [];
                } else {
                    $config = $configInput;
                }

                // TRANSFORM FOR DATABASE: Adapter for Data Structure Mismatch
                if (in_array($sectionData['section_key'], ['about', 'about_content', 'why_choose_us'])) {
                    if (isset($config['images']) && is_array($config['images'])) {
                        \Illuminate\Support\Facades\Log::info("PageBuilder: Transforming Images for " . $sectionData['section_key'], ['original' => $config['images']]);
                        $flatImages = [];
                        foreach ($config['images'] as $img) {
                            if (is_array($img)) {
                                if (isset($img['image'])) {
                                    $flatImages[] = $img['image'];
                                } elseif (isset($img['url'])) {
                                    $flatImages[] = $img['url'];
                                }
                            } elseif (is_string($img)) {
                                $flatImages[] = $img;
                            }
                        }
                        $config['images'] = $flatImages;
                        \Illuminate\Support\Facades\Log::info("PageBuilder: Final Flattened Images", ['final' => $config['images']]);
                    }
                }

                // If it has a valid existing ID
                if (isset($sectionData['id']) && !str_starts_with((string)$sectionData['id'], 'new-')) {
                    $section = PageSection::find($sectionData['id']);

                    // Relaxed comparison (==) to handle string/int differences in drivers
                    if ($section && $section->page_id == $page->id) {
                        try {
                            // CLEANUP LOGIC: Check for replaced images
                            // We access $section->config (triggering accessor) to compare with new flattened config
                            $this->cleanupOldImages($section->config, $config);

                            $section->update([
                                'position' => $index,
                                'config' => $config,
                                'is_active' => $sectionData['is_active'] ?? true,
                            ]);
                            // Log specific updates for debugging
                            // \Illuminate\Support\Facades\Log::info("Updated section {$section->id}", ['pos' => $index]);
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error("PageBuilder: Error updating section {$section->id}: " . $e->getMessage());
                            throw $e; // Re-throw to rollback transaction
                        }
                    } else {
                        // Detailed logging for debugging
                         $foundProps = $section ? "Found (PageID: {$section->page_id})" : "Not Found";
                         \Illuminate\Support\Facades\Log::warning("PageBuilder: Update Skipped for ID {$sectionData['id']}. Section: {$foundProps}. Expected PageID: {$page->id}");
                    }
                } else {
                    // Create new
                    $newSection = $page->sections()->create([
                        'section_key' => $sectionData['section_key'],
                        'position' => $index,
                        'config' => $config,
                        'is_active' => $sectionData['is_active'] ?? true,
                    ]);
                    \Illuminate\Support\Facades\Log::info("PageBuilder: Created new section {$newSection->id}");
                }
            }
        });

        \Illuminate\Support\Facades\Log::info("PageBuilder: Successfully completed transaction for Page {$page->id}");

        return back()->with('success', 'Page saved successfully.');
    }

    /**
     * Handle Image Upload
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|image|mimes:jpeg,png,jpg,webp|max:5120', // Max 5MB, images only
            'old_url' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            // 1. Cleanup old file if provided (Immediate UI Replacement)
            if ($request->old_url) {
                // We only delete if it's a managed path and NOT currently in use as a committed asset
                // (Optional: for simplicity, we just delete if it's a managed path and different from new)
                $this->deleteImageFile($request->old_url);
            }

            $file = $request->file('file');
            
            // Double check extension/mime via UploadHelper
            $fullPath = \App\Helpers\UploadHelper::getSluggedFilename($file, 'page');
            $directory = dirname($fullPath);
            $filename = basename($fullPath);
            
            $path = $file->storeAs($directory, $filename, 'public');
            
            return response()->json(['url' => '/storage/' . $path]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    /**
     * Compare old and new config, delete images that are removed.
     */
    protected function cleanupOldImages($oldConfig, $newConfig)
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
    protected function extractImages($data)
    {
        $images = [];

        if (is_array($data)) {
            foreach ($data as $value) {
                $images = array_merge($images, $this->extractImages($value));
            }
        } elseif (is_string($data)) {
            // Check if it matches our storage paths
            $managedPaths = [
                '/storage/page/',
                '/storage/page-builder/',
                '/storage/hero-slides/',
                '/storage/slider-images/',
                '/storage/about-images/',
                '/storage/icons/',
                '/storage/wcu-images/',
                '/storage/brand-images/',
                '/storage/services/',
                '/storage/pages/',
                '/storage/uploads/',
                '/uploads/'
            ];

            foreach ($managedPaths as $path) {
                if (str_starts_with($data, $path)) {
                    $images[] = $data;
                    break;
                }
            }
        }

        return $images;
    }

    /**
     * Delete the physical file if it exists in storage.
     */
    protected function deleteImageFile($url)
    {
        // 1. Remove '/storage/' prefix to get relative disk path
        $path = str_replace('/storage/', '', $url);

        // 2. Handle legacy paths that might have started with /uploads/ directly
        if (str_starts_with($path, '/')) {
            $path = substr($path, 1);
        }

        // 3. SAFETY CHECK: Only delete if path starts with allowed folders
        // We DO NOT want to delete /assets/, /seeder/, or root files.
        $allowedFolders = [
            'page/',
            'page-builder/',
            'hero-slides/',
            'slider-images/',
            'about-images/',
            'icons/',
            'wcu-images/',
            'brand-images/',
            'services/',
            'pages/',
            'uploads/'
        ];
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
