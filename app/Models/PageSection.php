<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Page; // Added for the relationship

class PageSection extends Model
{
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
        static::retrieved(function (PageSection $section) {
            $config = $section->config;

            // Auto-fix legacy image structure (array of arrays -> array of strings)
            if (isset($config['images']) && is_array($config['images'])) {
                $hasLegacyStructure = false;
                $newImages = [];

                foreach ($config['images'] as $image) {
                    if (is_array($image) && isset($image['image'])) {
                        $hasLegacyStructure = true;
                        $newImages[] = $image['image'];
                    } else {
                        $newImages[] = $image;
                    }
                }

                if ($hasLegacyStructure) {
                    $config['images'] = $newImages;
                    $section->config = $config;
                    // We don't save here to avoid side effects on GET requests,
                    // but the form will see clean data and save it correctly on update.
                }
            }
        });
    }
}
