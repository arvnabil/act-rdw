<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class UploadHelper
{
    /**
     * Generate a slugified filename and handle duplicates with a counter.
     */
    public static function getSluggedFilename(UploadedFile $file, string $directory, ?string $property = null): string
    {
        // 1. Extract context from directory
        $directory = trim($directory, '/');
        $segments = explode('/', $directory);
        
        $contextCount = count($segments);
        if ($contextCount >= 2) {
            $relevantSegments = array_slice($segments, -2);
            $baseName = implode('-', $relevantSegments);
        } else {
            $baseName = end($segments);
        }

        $genericFolders = ['thumbnails', 'icons', 'featured', 'images', 'featured_image', 'content-media'];
        if (in_array($baseName, $genericFolders)) {
            $baseName = $contextCount > 1 ? $segments[$contextCount - 2] . '-' . $baseName : $baseName;
        }

        // 2. Get Original Filename (Slugified, without extension)
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $originalSlug = Str::slug($originalName);

        // 3. Append Branding & Timestamp (Year, Minutes, Seconds)
        // Format: [context]-[original-name]-activ-teknologi-[timestamp]
        $finalBase = $baseName . '-' . $originalSlug . '-activ-teknologi-' . date('Y-is');
        $filename = Str::slug($finalBase);
        
        // 4. Force WebP Extension
        $extension = 'webp';
        
        // 5. WebP Conversion Logic (GD)
        try {
            $tempPath = $file->getRealPath();
            $mimeType = $file->getMimeType();
            $image = null;

            if (str_contains($mimeType, 'jpeg') || str_contains($mimeType, 'jpg')) {
                $image = imagecreatefromjpeg($tempPath);
            } elseif (str_contains($mimeType, 'png')) {
                $image = imagecreatefrompng($tempPath);
                // Keep transparency
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            }

            if ($image) {
                // Save converted image back to the same temp path but as webp format
                // Note: We use the same path to let Filament handle the final move
                imagewebp($image, $tempPath, 80); // quality 80
                imagedestroy($image);
            }
        } catch (\Exception $e) {
            // If conversion fails, fallback to original extension
            $extension = $file->getClientOriginalExtension();
        }
        
        $basePath = $directory . '/' . $filename . '.' . $extension;

        $counter = 1;
        $finalPath = $basePath;
        
        // 5. Check for duplicates (Unique handling)
        while (Storage::disk('public')->exists($finalPath)) {
            $finalPath = $directory . '/' . $filename . '-(' . $counter . ').' . $extension;
            $counter++;
        }

        return $finalPath;
    }
}
