<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Page; // Added for the relationship

use App\Traits\HasImageCleanup;

class PageSection extends Model
{
    use HasImageCleanup;

    protected $cleanupFields = ['config'];

    protected $fillable = [
        'page_id',
        'section_key',
        'position',
        'is_active',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    protected static function booted(): void
    {
        // Removed retrieved hook to prevent interference with saving.
        // Data sanitization is now handled by getConfigAttribute.
    }

    // Accessor to auto-fix legacy image structure on read
    public function getConfigAttribute($value)
    {
        // If it's a JSON string (raw attribute), decode it first
        if (is_string($value)) {
            $decoded = json_decode($value, true);
        } else {
            $decoded = $value;
        }

        if (!is_array($decoded)) {
            return $decoded ?: [];
        }

        // Auto-fix legacy image structure (array of arrays -> array of strings)
        if (isset($decoded['images']) && is_array($decoded['images'])) {
            $newImages = [];
            foreach ($decoded['images'] as $image) {
                if (is_array($image) && isset($image['image'])) {
                    $newImages[] = $image['image'];
                } else {
                    $newImages[] = $image;
                }
            }
            $decoded['images'] = $newImages;
        }

        return $decoded;
    }
}
