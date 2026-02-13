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
        'breadcrumb_image', // Added for custom breadcrumb thumbnail
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

    protected static function boot()
    {
        parent::boot();

        // Handle file cleanup when breadcrumb_image is updated or deleted
        static::updating(function ($model) {
            if ($model->isDirty('breadcrumb_image') && ($oldImage = $model->getOriginal('breadcrumb_image'))) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldImage);
            }
        });

        static::deleting(function ($model) {
            if ($model->breadcrumb_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($model->breadcrumb_image);
            }
        });
    }
}
