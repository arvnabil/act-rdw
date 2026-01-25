<?php

namespace Modules\ServiceSolutions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Models\Product;
use App\Traits\HasSeoMeta;


class Service extends Model
{
    use HasFactory, HasSeoMeta;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'content',
        'excerpt',
        'featured_image',
        'thumbnail',
        'icon',
        'hero_subtitle',
        'grid_title',
        'sort_order'
    ];

    // A service has many products (e.g. Surveillance sets)
    public function products()
    {
        return $this->hasMany(Product::class);
    }



    public function categories()
    {
        return $this->hasMany(ServiceCategory::class);
    }

    public function solutions()
    {
        return $this->hasMany(ServiceSolution::class);
    }
}
