<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMonitoringRecord extends Model
{
    protected $fillable = [
        'url',
        'path',
        'model',
        'model_id',
        'is_noindex',
        'in_sitemap',
        'canonical_valid',
        'seo_score',
        'priority',
        'changefreq',
        'last_modified',
    ];
}
