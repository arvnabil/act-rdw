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
        Log::debug("ImageHelper: Starting processAndConvert for target: {$targetPathWithoutExtension}");

        if (empty($contents)) {
            Log::warning("ImageHelper: Zero-length content provided.");
            return null;
        }

        try {
            $imagick = new \Imagick();
            $imagick->readImageBlob($contents);
            Log::debug("ImageHelper: Image blob read successfully.");

            // Auto-orient
            $orientation = $imagick->getImageOrientation();
            if ($orientation !== \Imagick::ORIENTATION_TOPLEFT) {
                Log::info("ImageHelper: Auto-orienting image from orientation: {$orientation}");
                switch($orientation) {
                    case \Imagick::ORIENTATION_BOTTOMRIGHT: $imagick->rotateimage("#000", 180); break;
                    case \Imagick::ORIENTATION_RIGHTTOP: $imagick->rotateimage("#000", 90); break;
                    case \Imagick::ORIENTATION_LEFTBOTTOM: $imagick->rotateimage("#000", -90); break;
                }
                $imagick->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);
                Log::debug("ImageHelper: Image auto-oriented.");
            } else {
                Log::debug("ImageHelper: Image already in TOPLEFT orientation, no auto-orientation needed.");
            }

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
                    Log::debug("ImageHelper: Set image format to WebP with quality 80.");
                    
                    // Handle Alpha Channel for WebP
                    if (in_array($originalFormat, ['png', 'gif', 'webp', 'avif'])) {
                        $imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_ACTIVATE);
                        $imagick->setBackgroundColor(new \ImagickPixel('transparent'));
                        Log::debug("ImageHelper: Activated alpha channel and set background to transparent for WebP conversion.");
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

    /**
     * Extracts a local relative path from a full URL if it belongs to the current application.
     * Returns the relative path IF the file exists on the local disk AND the domain matches APP_URL.
     * Returns null for external domains (forcing a download).
     */
    public static function getLocalPathFromUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        // 1. If it's already a relative path that exists in storage, return it
        if (!str_starts_with($url, 'http')) {
            $cleanPath = ltrim($url, '/');
            // Remove 'storage/' prefix if it's there
            if (str_starts_with($cleanPath, 'storage/')) {
                $cleanPath = substr($cleanPath, 8);
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath)) {
                Log::debug("ImageHelper: Path '{$url}' identified as direct local storage path.");
                return $cleanPath;
            }
            return null;
        }

        $appUrl = rtrim(config('app.url'), '/');
        $storageUrl = rtrim(\Illuminate\Support\Facades\Storage::url('/'), '/');
        
        // Normalize URLs for comparison
        $urlParts = parse_url($url);
        $appParts = parse_url($appUrl);
        
        $urlHost = strtolower($urlParts['host'] ?? '');
        $appHost = strtolower($appParts['host'] ?? '');

        // STRICT DOMAIN CHECK: If hosts don't match, it's EXTERNAL
        if (empty($urlHost) || $urlHost !== $appHost) {
            Log::info("ImageHelper: URL '{$url}' host '{$urlHost}' does NOT match app host '{$appHost}'. Identified as EXTERNAL.");
            return null;
        }

        Log::info("ImageHelper: URL '{$url}' host matches app host '{$appHost}'. Identified as INTERNAL.");

        $path = $urlParts['path'] ?? '';
        
        // If storageUrl is absolute (contains http), check it
        if (str_starts_with($storageUrl, 'http')) {
            $storageParts = parse_url($storageUrl);
            $storagePath = rtrim($storageParts['path'] ?? '', '/');
            
            if (!empty($storagePath) && str_starts_with($path, $storagePath)) {
                $relativePath = ltrim(substr($path, strlen($storagePath)), '/');
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($relativePath)) {
                    return $relativePath;
                }
            }
        } else {
            // storageUrl is relative like '/storage/'
            $storagePath = rtrim($storageUrl, '/');
            if (!empty($storagePath) && str_starts_with($path, $storagePath)) {
                $relativePath = ltrim(substr($path, strlen($storagePath)), '/');
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($relativePath)) {
                    return $relativePath;
                }
            }
        }
        
        Log::warning("ImageHelper: URL '{$url}' identified as INTERNAL but file not found in public storage.");
        return null;
    }

    /**
     * Resolves an image URL/path from import data into a relative storage path.
     * 
     * Logic:
     * - If blank: returns $fallback
     * - If local URL (same domain): extracts relative path
     * - If external URL: downloads, converts to WebP, saves to $targetDir
     * - If relative path: returns as-is
     *
     * @param string|null $value     The image value from CSV (URL, path, or blank)
     * @param string      $targetDir Target directory for saving downloaded images (e.g. 'seo/og')
     * @param string      $slug      Slug for naming the file
     * @param string|null $fallback  Fallback value if $value is blank
     * @return string|null           Relative storage path or null
     */
    public static function resolveImageFromUrl(?string $value, string $targetDir, string $slug, ?string $fallback = null): ?string
    {
        Log::debug("--- resolveImageFromUrl START --- Value: [{$value}] | Target: [{$targetDir}] | Slug: [{$slug}] | Fallback: [{$fallback}]");
        if (blank($value)) {
            Log::debug("resolveImageFromUrl: Value is blank. Returning fallback: " . ($fallback ?: 'NULL'));
            return $fallback;
        }

        // Not a URL — treat as relative path
        if (!filter_var($value, FILTER_VALIDATE_URL) && !str_starts_with($value, 'http')) {
            return $value;
        }

        // Check if it's a local URL
        $localPath = self::getLocalPathFromUrl($value);
        if ($localPath) {
            Log::info("resolveImageFromUrl: Local URL resolved to: {$localPath}");
            return $localPath;
        }

        // External URL — download and convert
        Log::info("resolveImageFromUrl: Downloading external URL: {$value}");
        try {
            $cleanUrl = str_replace(' ', '%20', $value);

            $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                ])
                ->timeout(30)
                ->retry(2, 1000)
                ->get($cleanUrl);

            if ($response->successful()) {
                $contents = $response->body();
                $filename = \Illuminate\Support\Str::slug($slug ?: 'image') . '-' . time();
                $targetPath = rtrim($targetDir, '/') . '/' . $filename;

                $savedPath = self::processAndConvert($contents, $targetPath);
                if ($savedPath) {
                    Log::info("resolveImageFromUrl: Saved to: {$savedPath}");
                    return $savedPath;
                }
            } else {
                Log::warning("resolveImageFromUrl: Download failed. Status: " . $response->status());
            }
        } catch (\Throwable $e) {
            Log::error("resolveImageFromUrl: Error: " . $e->getMessage());
        }

        // If download failed, return fallback
        return $fallback;
    }
}
