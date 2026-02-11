<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasImageCleanup;

class EventDocumentation extends Model
{
    use HasFactory, HasImageCleanup;
    protected $cleanupFields = ['file_path'];

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
