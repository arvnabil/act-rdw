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
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        $originalExtension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($originalExtension, $allowedExtensions)) {
             throw \Illuminate\Validation\ValidationException::withMessages([
                 $property ?? 'file' => "File extension '{$originalExtension}' tidak didukung. Harap gunakan: " . implode(', ', $allowedExtensions) . "."
             ]);
        }

        if ($originalExtension === 'svg') {
            $basePath = $directory . '/' . $filename . '.svg';
            $finalPath = $basePath;
            $counter = 1;
            while (Storage::disk('public')->exists($finalPath)) {
                $finalPath = $directory . '/' . $filename . '-(' . $counter . ').svg';
                $counter++;
            }
            return $finalPath;
        }

        $extension = 'webp';
        
        // 5. WebP Conversion Logic (GD)
        try {
            $tempPath = $file->getRealPath();
            $mimeType = $file->getMimeType();
            $image = null;

            if (str_contains($mimeType, 'jpeg') || str_contains($mimeType, 'jpg')) {
                $image = @imagecreatefromjpeg($tempPath);
            } elseif (str_contains($mimeType, 'png')) {
                $image = @imagecreatefrompng($tempPath);
                if ($image) {
                    // Keep transparency
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
            } elseif (str_contains($mimeType, 'webp')) {
                 $image = @imagecreatefromwebp($tempPath);
            }

            if ($image) {
                // Save converted image back to the same temp path but as webp format
                imagewebp($image, $tempPath, 80); // quality 80
                imagedestroy($image);
            } else {
                 // If image creation failed despite mime check (corrupt or forge), reject
                 throw \Illuminate\Validation\ValidationException::withMessages([
                     $property ?? 'file' => "File gambar tidak valid atau korup (Gagal memproses WebP)."
                 ]);
            }
        } catch (\Exception $e) {
            // Re-throw security violations, only fallback for non-critical conversion issues if necessary (but here we want to be strict)
            throw $e;
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
