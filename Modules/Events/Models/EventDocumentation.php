<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventDocumentation extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'type', // image, presentation
        'file_path',
        'caption',
    ];

    protected $casts = [
        'file_path' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
