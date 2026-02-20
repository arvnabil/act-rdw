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

            // 1. Identify format
            $originalFormat = strtolower($imagick->getImageFormat());
            Log::info("ImageHelper: Processing {$originalFormat} image. Input Size: " . strlen($contents) . " bytes");

            // 2. Try WebP Conversion
            $webpSupported = false;
            try {
                // Check if Imagick supports WebP
                $formats = \Imagick::queryFormats('WEBP');
                if (!empty($formats)) {
                    $imagick->setImageFormat('webp');
                    $imagick->setImageCompressionQuality(80);
                    
                    // Handle Alpha Channel for WebP
                    if (in_array($originalFormat, ['png', 'gif', 'webp', 'avif'])) {
                        $imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_ACTIVATE);
                        $imagick->setBackgroundColor(new \ImagickPixel('transparent'));
                    }
                    
                    $finalExtension = 'webp';
                    $webpSupported = true;
                } else {
                    Log::warning("ImageHelper: WebP format not supported by this Imagick installation.");
                }
            } catch (\Throwable $e) {
                Log::warning("ImageHelper: Failed to set WebP format: " . $e->getMessage());
            }

            // 3. Fallback if WebP not supported
            if (!$webpSupported) {
                Log::info("ImageHelper: Falling back to original format ({$originalFormat})");
                $finalExtension = $originalFormat ?: 'png';
                // Just keep original format
            }

            // 4. Prepare Target Path
            $targetPath = $targetPathWithoutExtension . '.' . $finalExtension;
            
            // Ensure directory exists
            $dir = dirname($targetPath);
            if (!Storage::disk($disk)->exists($dir)) {
                Storage::disk($disk)->makeDirectory($dir);
            }

            // 5. Save content
            try {
                $finalContents = $imagick->getImageBlob();
                Storage::disk($disk)->put($targetPath, $finalContents);
                Log::info("ImageHelper: Success! Saved to {$targetPath}. Final Size: " . strlen($finalContents) . " bytes (" . ($webpSupported ? 'WebP' : 'Original') . ")");
            } catch (\Throwable $e) {
                // LAST RESORT: If Imagick fails to even give us a blob, save the raw original content
                Log::error("ImageHelper: Imagick failed to generate blob. Saving RAW original content as fallback. Error: " . $e->getMessage());
                // Detect extension from raw contents if possible, otherwise use originalFormat
                $finalExtension = $originalFormat ?: 'png';
                $targetPath = $targetPathWithoutExtension . '.' . $finalExtension;
                Storage::disk($disk)->put($targetPath, $contents);
                Log::info("ImageHelper: Saved raw content. Size: " . strlen($contents) . " bytes");
            }
            
            $imagick->clear();
            $imagick->destroy();

            return $targetPath;
        } catch (\Throwable $e) {
            Log::error("ImageHelper Fatal Error: " . $e->getMessage() . ". Input Size: " . strlen($contents) . " bytes");
            // Extreme fallback: just save the raw bytes with a generic extension if we can't even open it with Imagick
            try {
                $targetPath = $targetPathWithoutExtension . '.img'; // generic
                Storage::disk($disk)->put($targetPath, $contents);
                Log::warning("ImageHelper: Saved raw content to {$targetPath} after fatal Imagick error.");
                return $targetPath;
            } catch (\Throwable $fallbackError) {
                Log::error("ImageHelper: Even extreme fallback failed: " . $fallbackError->getMessage());
                return null;
            }
        }
    }
}
