<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Page; // Added for the relationship

class PageSection extends Model
{
    protected $fillable = [
        'page_id',
        'section_key',
        'position',
        'is_active',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
