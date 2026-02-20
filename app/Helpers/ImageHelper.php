<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageHelper
{
    /**
     * Processes binary image content, validates it, and converts it to WebP.
     * Returns the relative path to the saved WebP file, or null on failure.
     */
    public static function processAndConvert(string $contents, string $targetPathWithoutExtension, string $disk = 'public'): ?string
    {
        if (empty($contents)) {
            Log::warning("ImageHelper: Zero-length content provided.");
            return null;
        }

        try {
            $imagick = new \Imagick();
            $imagick->readImageBlob($contents);

            // 1. Identify format and Log
            $format = strtolower($imagick->getImageFormat());
            Log::info("ImageHelper: Processing {$format} image. Size: " . strlen($contents) . " bytes");

            // 2. Prepare for WebP
            $imagick->setImageFormat('webp');
            $imagick->setImageCompressionQuality(80);

            // 3. Handle Alpha Channel (Maintain transparency)
            // If it's a format that typically has transparency (PNG, GIF), ensure it's kept
            if (in_array($format, ['png', 'gif', 'webp', 'avif'])) {
                $imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_ACTIVATE);
                $imagick->setBackgroundColor(new \ImagickPixel('transparent'));
            }

            // 4. Prepare Target Path
            $targetPath = $targetPathWithoutExtension . '.webp';
            
            // Ensure directory exists
            $dir = dirname($targetPath);
            if (!Storage::disk($disk)->exists($dir)) {
                Storage::disk($disk)->makeDirectory($dir);
            }

            // 5. Save using Laravel Storage to maintain consistency (Cloud/Local)
            // Imagick can't write directly to Laravel's virtual storage, so we get the blob
            $webpContents = $imagick->getImageBlob();
            Storage::disk($disk)->put($targetPath, $webpContents);

            Log::info("ImageHelper: Success! Saved to {$targetPath} via Imagick");
            
            $imagick->clear();
            $imagick->destroy();

            return $targetPath;
        } catch (\Throwable $e) {
            Log::error("ImageHelper Imagick Error: " . $e->getMessage() . " | Format: " . ($format ?? 'unknown'));
            return null;
        }
    }
}
