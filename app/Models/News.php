<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasSeoMeta;
use App\Traits\HasImageCleanup;

class News extends Model
{
    use HasFactory, HasSeoMeta, HasImageCleanup;

    protected $cleanupFields = ['featured_image'];

    protected $guarded = ['id'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function categories()
    {
        return $this->belongsToMany(NewsCategory::class, 'news_category_post');
    }

    public function tags()
    {
        return $this->belongsToMany(NewsTag::class, 'news_tag_post');
    }
}
