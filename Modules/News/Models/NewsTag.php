<?php

namespace Modules\News\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsTag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function posts()
    {
        return $this->belongsToMany(News::class, 'news_tag_post', 'news_tag_id', 'news_id');
    }
}
