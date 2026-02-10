<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasSeoMeta;
use App\Traits\HasImageCleanup;

class Client extends Model
{
    use HasFactory, HasSeoMeta, HasImageCleanup;

    protected $cleanupFields = ['logo'];

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'website_url',
        'category',
        'is_active',
        'position',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
