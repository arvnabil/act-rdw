<?php

namespace Modules\Events\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Events\Models\EventCertificate;

class CertificateDesignerController extends Controller
{
    public function edit($id)
    {
        $certificate = EventCertificate::with('event')->findOrFail($id);

        return Inertia::render('Events/Certificate/Filaments/CertificateDesigner', [
            'certificate' => $certificate,
            'event' => $certificate->event,
        ]);
    }

    public function update(Request $request, $id)
    {
        $certificate = EventCertificate::findOrFail($id);

        $validated = $request->validate([
            'content_layout' => 'required|array',
            'image_files' => 'nullable|array', // Now using image_files
            'certificate_background' => 'nullable|string',
        ]);

        $certificate->update([
            'content_layout' => $validated['content_layout'],
            'image_files' => isset($validated['image_files']) 
                ? collect($validated['image_files'])->map(function($file) {
                    return is_array($file) ? ($file['path'] ?? null) : $file;
                })->filter()->values()->toArray()
                : $certificate->image_files,
        ]);

        return back()->with('success', 'Design saved successfully!');
    }

    private function getAssetDirectory($certificateId)
    {
        $certificate = \Modules\Events\Models\EventCertificate::with('event')->findOrFail($certificateId);
        $slug = $certificate->event->slug ?? 'default';
        return "certificates/assets/{$slug}";
    }

    public function indexMedia($id)
    {
        $directory = $this->getAssetDirectory($id);
        
        // Ensure directory exists
        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($directory)) {
            \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory($directory);
        }

        $files = \Illuminate\Support\Facades\Storage::disk('public')->files($directory);
        
        $assets = collect($files)->map(function ($path) {
            return [
                'name' => basename($path),
                'url' => asset('storage/' . $path),
                'path' => $path
            ];
        })->values();

        return response()->json($assets);
    }

    public function uploadMedia(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|image|max:2048', // 2MB max
        ]);

        $directory = $this->getAssetDirectory($id);
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $path = $directory . '/' . $filename;
        
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = $name . '_' . time() . '.' . $extension;
        }

        // Store with original (or unique) name in slug directory
        $path = $file->storeAs($directory, $filename, 'public');
        $url = asset('storage/' . $path);

        return response()->json([
            'name' => basename($path),
            'url' => $url,
            'path' => $path
        ]);
    }

    public function renameMedia(Request $request, $id)
    {
        $request->validate([
            'old_path' => 'required|string',
            'new_name' => 'required|string|min:1',
        ]);

        $directory = $this->getAssetDirectory($id);
        $oldPath = $request->old_path;
        
        // Security check: ensure old path belongs to the correct directory
        if (dirname($oldPath) !== $directory) {
             // Fallback or strict check? Let's just strict check for safety
             // Actually, dirname on windows/linux might act differently, 
             // let's trust the old_path passed from frontend which came from indexMedia
             // But for safety, we should enforce the directory from the ID.
            return response()->json(['error' => 'Invalid file path for this certificate'], 403);
        }

        $newName = $request->new_name;
        $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
        if (pathinfo($newName, PATHINFO_EXTENSION) !== $extension) {
            $newName .= '.' . $extension;
        }

        $newPath = $directory . '/' . $newName;

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($newPath)) {
            return response()->json(['error' => 'File with this name already exists'], 422);
        }

        if (\Illuminate\Support\Facades\Storage::disk('public')->move($oldPath, $newPath)) {
            return response()->json([
                'success' => true,
                'name' => basename($newPath),
                'url' => asset('storage/' . $newPath),
                'path' => $newPath
            ]);
        }

        return response()->json(['error' => 'Failed to rename file'], 500);
    }

    public function deleteMedia(Request $request, $id)
    {
        $request->validate([
            'path' => 'nullable|string',
            'paths' => 'nullable|array',
            'paths.*' => 'string',
        ]);

        $pathsToDelete = [];
        if ($request->has('paths')) {
            $pathsToDelete = $request->paths;
        } elseif ($request->has('path')) {
            $pathsToDelete = [$request->path];
        }

        if (empty($pathsToDelete)) {
             return response()->json(['error' => 'No files specified'], 422);
        }

        foreach ($pathsToDelete as $path) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            }
        }

        return response()->json(['success' => true]);
    }
}
