<?php

namespace Modules\CMS\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\CMS\Models\Page; // Added for the relationship

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

        // Normalize image repeaters: Ensure they are arrays of objects
        // Provides both 'url' and 'image' keys for compatibility with various frontend components.
        if (isset($decoded['images']) && is_array($decoded['images'])) {
            $normalizedImages = [];
            foreach ($decoded['images'] as $item) {
                if (is_array($item)) {
                    // Enrich object if one key is missing
                    if (isset($item['url']) && !isset($item['image'])) $item['image'] = $item['url'];
                    if (isset($item['image']) && !isset($item['url'])) $item['url'] = $item['image'];
                    $normalizedImages[] = $item;
                } elseif (is_string($item)) {
                    // Wrap string in object with both keys
                    $normalizedImages[] = [
                        'url' => $item,
                        'image' => $item
                    ];
                }
            }
            $decoded['images'] = $normalizedImages;
        }

        // Auto-parse SEO links in content/description fields
        $contentFields = ['content', 'description', 'text', 'body'];
        foreach ($contentFields as $field) {
            if (isset($decoded[$field]) && is_string($decoded[$field])) {
                $decoded[$field] = \Modules\SEO\Helpers\SeoHelper::parse_links($decoded[$field]);
            }
        }

        // Auto-generate rel for URL fields
        foreach ($decoded as $key => $value) {
            if (is_string($value) && (str_ends_with($key, '_url') || $key === 'link')) {
                $decoded[$key . '_rel'] = \Modules\SEO\Helpers\SeoHelper::get_rel($value);
            }
        }

        return $decoded;
    }
}
