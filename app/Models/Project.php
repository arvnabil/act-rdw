<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasSeoMeta;
use App\Traits\HasImageCleanup;

class Project extends Model
{
    use HasFactory, HasSeoMeta, HasImageCleanup;

    public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function brands()
    {
        return $this->belongsToMany(\Modules\Core\Models\Brand::class, 'project_brand');
    }

    public function solutions()
    {
        return $this->belongsToMany(\Modules\ServiceSolutions\Models\ServiceSolution::class, 'project_service_solution');
    }

    protected $cleanupFields = ['thumbnail', 'download_brochures'];
    protected $richEditorCleanupFields = ['content'];

    protected $guarded = ['id'];

    protected $casts = [
        'published_at' => 'datetime',
        'project_date' => 'date',
        'download_brochures' => 'array',
        'tags' => 'array',
    ];

    public function getContentAttribute($value)
    {
        return \App\Helpers\SeoHelper::parse_links($value);
    }
}
