<?php

namespace Modules\News\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Modules\SEO\Traits\HasSeoMeta;
use App\Traits\HasImageCleanup;

class News extends Model
{
    use HasFactory, HasSeoMeta, HasImageCleanup;

    protected $cleanupFields = ['featured_image', 'breadcrumb_image'];
    protected $richEditorCleanupFields = ['content'];

    protected $guarded = ['id'];

    protected $casts = [
        'published_at' => 'datetime',
        'show_breadcrumb' => 'boolean',
    ];

    public function getContentAttribute($value)
    {
        return \Modules\SEO\Helpers\SeoHelper::parse_links($value);
    }

    public function categories()
    {
        return $this->belongsToMany(NewsCategory::class, 'news_category_post', 'news_id', 'news_category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(NewsTag::class, 'news_tag_post', 'news_id', 'news_tag_id');
    }

    public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
