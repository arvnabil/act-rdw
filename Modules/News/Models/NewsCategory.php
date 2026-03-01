<?php

namespace Modules\News\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsCategory extends Model
{
    use HasFactory, \Modules\SEO\Traits\HasSeoMeta;

    protected $fillable = ['name', 'slug'];

    public function posts()
    {
        return $this->belongsToMany(News::class, 'news_category_post', 'news_category_id', 'news_id');
    }
}
