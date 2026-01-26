<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
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
            $result = \App\Services\Seo\SeoScoreCalculator::calculate($seoMeta);
            $seoMeta->seo_score = $result['score'];
        });
    }

    public function seoable()
    {
        return $this->morphTo();
    }
}
