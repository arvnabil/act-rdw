<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PageSection;

use App\Traits\HasSeoMeta;

class Page extends Model
{
    use HasSeoMeta;

    protected $fillable = [
        'title',
        'slug',
        'type',
        'status',
        'is_homepage',
        'show_breadcrumb',
        'parent_id',
        'menu_order',
    ];

    protected $casts = [
        'is_homepage' => 'boolean',
        'show_breadcrumb' => 'boolean',
    ];

    public function sections()
    {
        return $this->hasMany(PageSection::class)->orderBy('position');
    }

    public function seo()
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }
}
