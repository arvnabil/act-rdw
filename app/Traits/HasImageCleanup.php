<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

trait HasImageCleanup
{
    protected static function bootHasImageCleanup(): void
    {
        static::updated(function (Model $model) {
            // 1. Standard Fields
            foreach ($model->getCleanupFields() as $field) {
                if ($model->isDirty($field)) {
                    $oldValue = $model->getOriginal($field);
                    $newValue = $model->getAttribute($field);
                    self::cleanupOrphanedFiles($oldValue, $newValue);
                }
            }

            // 2. RichEditor Fields
            foreach ($model->getRichEditorCleanupFields() as $field) {
                if ($model->isDirty($field)) {
                    $oldHtml = $model->getOriginal($field);
                    $newHtml = $model->getAttribute($field);
                    self::cleanupOrphanedHtmlImages($oldHtml, $newHtml);
                }
            }
        });

        static::deleted(function (Model $model) {
            foreach ($model->getCleanupFields() as $field) {
                $value = $model->getAttribute($field);
                self::cleanupOrphanedFiles($value, null);
            }

            foreach ($model->getRichEditorCleanupFields() as $field) {
                $html = $model->getAttribute($field);
                self::cleanupOrphanedHtmlImages($html, null);
            }
        });
    }

    /**
     * Cleanup images extracted from HTML.
     */
    protected static function cleanupOrphanedHtmlImages($oldHtml, $newHtml): void
    {
        $oldPaths = self::extractImagesFromHtml($oldHtml);
        $newPaths = self::extractImagesFromHtml($newHtml);

        $orphaned = array_diff($oldPaths, $newPaths);

        foreach ($orphaned as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * Extract image paths from HTML <img> tags.
     */
    protected static function extractImagesFromHtml($html): array
    {
        if (empty($html)) return [];
        
        $images = [];
        preg_match_all('/<img[^>]+src="([^">]+)"/', $html, $matches);
        
        if (isset($matches[1])) {
            foreach ($matches[1] as $url) {
                if (\Illuminate\Support\Str::contains($url, '/storage/')) {
                    $images[] = \Illuminate\Support\Str::after($url, '/storage/');
                } else {
                    $images[] = $url;
                }
            }
        }
        
        return array_unique($images);
    }

    /**
     * Cleanup files that exist in $old but not in $new.
     */
    protected static function cleanupOrphanedFiles($old, $new): void
    {
        $oldPaths = self::extractPaths($old);
        $newPaths = self::extractPaths($new);

        $orphaned = array_diff($oldPaths, $newPaths);

        foreach ($orphaned as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * Recursively extract potential file paths from string or array.
     */
    protected static function extractPaths($value): array
    {
        if (empty($value)) {
            return [];
        }

        if (is_string($value)) {
            $paths = [];
            
            // 1. Check if it's a direct file path
            // Strip any "/storage/" prefix if provided
            $checkValue = $value;
            if (\Illuminate\Support\Str::contains($checkValue, '/storage/')) {
                $checkValue = \Illuminate\Support\Str::after($checkValue, '/storage/');
            }

            if (preg_match('/\.(jpg|jpeg|png|gif|svg|webp|pdf|doc|docx|zip)$/i', $checkValue)) {
                $paths[] = $checkValue;
            }

            // 2. Also try to extract images from HTML (if it's HTML)
            $htmlPaths = self::extractImagesFromHtml($value);
            $paths = array_merge($paths, $htmlPaths);

            return array_unique($paths);
        }

        if (is_array($value)) {
            $paths = [];
            foreach ($value as $item) {
                $paths = array_merge($paths, self::extractPaths($item));
            }
            return array_unique($paths);
        }

        return [];
    }

    public function getCleanupFields(): array
    {
        return property_exists($this, 'cleanupFields') ? $this->cleanupFields : [];
    }

    public function getRichEditorCleanupFields(): array
    {
        return property_exists($this, 'richEditorCleanupFields') ? $this->richEditorCleanupFields : [];
    }
}
