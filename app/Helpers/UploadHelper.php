<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UploadHelper
{
    /**
     * Generate a slugified filename and handle duplicates with a counter.
     */
    public static function getSluggedFilename(TemporaryUploadedFile $file, string $directory): string
    {
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        
        // Ensure directory doesn't have double slashes
        $directory = trim($directory, '/');
        
        $basePath = $directory . '/' . $filename . '.' . $extension;

        $counter = 1;
        $finalPath = $basePath;
        
        // Check for duplicates in public disk
        while (Storage::disk('public')->exists($finalPath)) {
            $finalPath = $directory . '/' . $filename . '-(' . $counter . ').' . $extension;
            $counter++;
        }

        return $finalPath;
    }
}
