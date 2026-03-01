<?php

namespace Modules\SEO\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoWhitelistDomain extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain',
        'description',
        'is_active',
    ];
}
