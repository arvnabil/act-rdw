<?php

namespace Modules\Events\Models;

use Modules\Events\Models\EventUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'certificate_background',
        'content_layout',
        'image_files', // JSON Array of image paths
    ];

    protected $casts = [
        'content_layout' => 'array',
        'image_files' => 'array',
    ];

    protected static function booted()
    {
        static::deleting(function ($certificate) {
            $slug = $certificate->event->slug ?? 'default';
            $directory = "certificates/assets/{$slug}";

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($directory)) {
                \Illuminate\Support\Facades\Storage::disk('public')->deleteDirectory($directory);
            }
        });

        static::updating(function ($certificate) {
            // Handle image_files array cleanup
            $originalFiles = $certificate->getOriginal('image_files') ?? [];
            // Ensure array format even if raw JSON
            if (is_string($originalFiles)) {
                $originalFiles = json_decode($originalFiles, true) ?? [];
            }
            
            $newFiles = $certificate->image_files ?? [];
            
            // Helper to extract paths safely
            $extractPaths = function ($files) {
                if (empty($files)) return [];
                return array_map(function ($file) {
                    if (is_array($file)) {
                        return $file['path'] ?? null;
                    }
                    return $file;
                }, $files);
            };

            $originalPaths = $extractPaths($originalFiles);
            $newPaths = $extractPaths($newFiles);
            
            // Filter out nulls
            $originalPaths = array_filter($originalPaths);
            $newPaths = array_filter($newPaths);

            // Find files that are in original but not in new (deleted/removed)
            $filesToDelete = array_diff($originalPaths, $newPaths);
            
            foreach ($filesToDelete as $file) {
                if ($file && \Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                     \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                }
            }
        
            // Handle certificate_background cleanup
            if ($certificate->isDirty('certificate_background')) {
                $originalBg = $certificate->getOriginal('certificate_background');
                if ($originalBg && \Illuminate\Support\Facades\Storage::disk('public')->exists($originalBg)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($originalBg);
                }
            }

            // Sync content_layout: Remove elements if file does not exist
            $layout = $certificate->content_layout ?? [];
            if (is_string($layout)) {
                $layout = json_decode($layout, true) ?? [];
            }

            $cleanLayout = $layout;
            $elements = $layout;
            $isStructured = false;

            // Check if it's the new structure (object with 'elements' key)
            if (isset($layout['elements']) && is_array($layout['elements'])) {
                $elements = $layout['elements'];
                $isStructured = true;
            }

            // Filter elements (legacy or structured)
            $cleanElements = collect($elements)->filter(function ($element) {
                // Keep non-image elements
                if (($element['type'] ?? '') !== 'image') {
                    return true;
                }

                $src = $element['src'] ?? '';
                if (empty($src)) return false;

                // Extract path from URL (naive approach assuming /storage/ works)
                $path = \Illuminate\Support\Str::contains($src, '/storage/') 
                        ? \Illuminate\Support\Str::after($src, '/storage/') 
                        : $src;

                return \Illuminate\Support\Facades\Storage::disk('public')->exists($path);
            })->values()->toArray();

            if ($isStructured) {
                $cleanLayout['elements'] = $cleanElements;
            } else {
                $cleanLayout = $cleanElements;
            }

            $certificate->content_layout = $cleanLayout;
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function userCertificates()
    {
        return $this->hasMany(EventUserCertificate::class);
    }
}
