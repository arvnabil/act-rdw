<?php

namespace Modules\Events\Models;

use Modules\Events\Models\EventUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasImageCleanup;

class EventCertificate extends Model
{
    use HasImageCleanup;

    protected $cleanupFields = ['certificate_background', 'image_files'];
    use HasFactory;

    protected $fillable = [
        'event_id',
        'certificate_background',
        'content_layout',
        'image_files', // JSON Array of image paths
    ];

    protected $casts = [
        'content_layout' => 'array',
        'image_files' => 'array',
    ];

    protected static function booted()
    {
        // Trait handles automatic cleanup of image_files and certificate_background on update and delete.
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function userCertificates()
    {
        return $this->hasMany(EventUserCertificate::class);
    }
}
