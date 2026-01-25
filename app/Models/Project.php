<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasSeoMeta;
use App\Traits\HasImageCleanup;

class Project extends Model
{
    use HasFactory, HasSeoMeta, HasImageCleanup;

    protected $cleanupFields = ['thumbnail'];

    protected $guarded = ['id'];

    protected $casts = [
        'published_at' => 'datetime',
        'project_date' => 'date',
        'download_brochures' => 'array',
    ];
}
