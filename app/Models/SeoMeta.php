<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasImageCleanup;

class SeoMeta extends Model
{
    use HasImageCleanup;

    protected $cleanupFields = ['og_image'];

    protected $table = 'seo_meta';

    protected $fillable = [
        'title',
        'description',
        'keywords',
        'og_title',
        'og_description',
        'og_image',
        'canonical_url',
        'noindex',
        'twitter_card',
        'schema_markup',
        'schema_override',
        'seo_score',
    ];

    protected $casts = [
        'schema_markup' => 'array',
        'keywords' => 'array',
        'seo_score' => 'integer',
        'noindex' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function ($seoMeta) {
            // Only calculate score if the parent model exists
            if ($seoMeta->seoable) {
                $auditService = app(\App\Services\Seo\SeoAuditService::class);
                $seoMeta->seo_score = $auditService->calculateScore($seoMeta->seoable);
            }
        });
    }


    public function seoable()
    {
        return $this->morphTo();
    }
}
