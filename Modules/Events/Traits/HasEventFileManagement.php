<?php

namespace Modules\Events\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasEventFileManagement
{
    /**
     * Define the file fields and their subdirectories.
     * Format: ['field_name' => 'subdirectory_name']
     * Example: ['thumbnail' => 'thumbnail', 'speakers' => 'speakers']
     * 
     * @return array
     */
    abstract protected function fileFields(): array;

    /**
     * Get the base storage path for files.
     * Example: "events/{$slug}"
     * 
     * @return string
     */
    abstract protected function getStorageBasePath(): string;

    protected static function bootHasEventFileManagement()
    {
        static::deleting(function (Model $model) {
            $model->cleanupFilesOnDelete();
        });

        static::updating(function (Model $model) {
            $model->cleanupFilesOnUpdate();
        });
    }

    protected function cleanupFilesOnDelete()
    {
        // If the model represents the entire directory (like Event), 
        // we might want to delete the whole folder.
        // However, usually we delete specific files unless it's the parent.
        
        // Strategy: Delegate to specific logic or iterate fields.
        // For Event: We want to delete "events/{slug}".
        // For Child: We want to delete the specific file.

        if (method_exists($this, 'shouldDeleteEntireDirectory') && $this->shouldDeleteEntireDirectory()) {
             $directory = $this->getStorageBasePath();
             if (Storage::disk('public')->exists($directory)) {
                 Storage::disk('public')->deleteDirectory($directory);
             }
             return; // Stop here if we nuked the directory
        }

        foreach ($this->fileFields() as $field => $subDir) {
            $value = $this->$field;
            $this->deleteFileOrFiles($value);
        }
    }

    /**
     * Define the RichEditor fields to monitor for image cleanup.
     * 
     * @return array
     */
    protected function richEditorFields(): array
    {
        return [];
    }

    protected function cleanupFilesOnUpdate()
    {
        // 1. Standard File Fields
        foreach ($this->fileFields() as $field => $subDir) {
            if ($this->isDirty($field)) {
                $original = $this->getOriginal($field);
                $current = $this->$field;
                
                // If array (JSON), compare diffs
                if ($this->isJsonCast($field)) {
                     $this->cleanupArrayFiles($original, $current);
                } else {
                     // Single string
                     if ($original && $original !== $current) {
                         $this->deleteFileOrFiles($original);
                     }
                }
            }
        }

        // 2. RichEditor Fields
        foreach ($this->richEditorFields() as $field) {
            if ($this->isDirty($field)) {
                $original = $this->getOriginal($field);
                $current = $this->$field;
                $this->cleanupRichEditorImages($original, $current);
            }
        }
    }

    protected function cleanupRichEditorImages($originalContent, $currentContent)
    {
        $originalImages = $this->extractImagesFromHtml($originalContent);
        $currentImages = $this->extractImagesFromHtml($currentContent);

        $toDelete = array_diff($originalImages, $currentImages);

        foreach ($toDelete as $image) {
            $this->deleteFileOrFiles($image);
        }
    }

    protected function extractImagesFromHtml($html)
    {
        if (empty($html)) return [];
        
        $images = [];
        preg_match_all('/<img[^>]+src="([^">]+)"/', $html, $matches);
        
        if (isset($matches[1])) {
            foreach ($matches[1] as $url) {
                // Convert URL to storage path
                // Example: http://domain.com/storage/events/x.jpg -> events/x.jpg
                // Example: /storage/events/x.jpg -> events/x.jpg
                
                if (Str::contains($url, '/storage/')) {
                    $images[] = Str::after($url, '/storage/');
                } else {
                    // Fallback for relative paths or direct storage paths if any
                    $images[] = $url;
                }
            }
        }
        
        return array_unique($images);
    }
    
    protected function cleanupArrayFiles($original, $current)
    {
        // Decode if string
        if (is_string($original)) $original = json_decode($original, true) ?? [];
        if (is_string($current)) $current = json_decode($current, true) ?? [];
        
        // Normalize to array of paths
        $originalPaths = $this->extractPaths($original);
        $newPaths = $this->extractPaths($current);
        
        $toDelete = array_diff($originalPaths, $newPaths);
        
        foreach ($toDelete as $path) {
            $this->deleteFileOrFiles($path);
        }
    }

    protected function extractPaths($data)
    {
        if (empty($data)) return [];
        if (!is_array($data)) return [$data];
        
        // Flatten or map
        // If array of objects (Media Library style), usually has 'path' or 'url'.
        // Or simple array of strings.
        
        $paths = [];
        foreach ($data as $item) {
            if (is_string($item)) {
                $paths[] = $item;
            } elseif (is_array($item)) {
                // Check common keys
                if (isset($item['path'])) $paths[] = $item['path'];
                elseif (isset($item['file_path'])) $paths[] = $item['file_path'];
            }
        }
        return $paths;
    }

    protected function deleteFileOrFiles($path)
    {
        if (empty($path)) return;
        
        // Naive check: if it's a full URL, try to extract path
        // Assuming 'storage/' is the delimiter for public disk
        /*
        if (Str::contains($path, '/storage/')) {
            $path = Str::after($path, '/storage/');
        }
        */
        // User request implies saving relative paths "public/events...", 
        // typically Filament saves "events/slug/file.jpg".
        // Ensure we don't try to delete full URLs unless mapped.
        
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
    
    protected function isJsonCast($key)
    {
        $casts = $this->getCasts();
        return isset($casts[$key]) && in_array($casts[$key], ['array', 'json', 'object', 'collection']);
    }
}
