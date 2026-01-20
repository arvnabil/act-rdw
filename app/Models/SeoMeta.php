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
    ];

    protected $casts = [
        'schema_markup' => 'array',
    ];

    public function seoable()
    {
        return $this->morphTo();
    }
}
