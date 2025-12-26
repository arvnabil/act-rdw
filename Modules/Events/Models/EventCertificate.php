<?php

namespace Modules\Events\Models;

use Modules\Events\Models\EventUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'certificate_background',
        'content_layout',
        'signature',
        'signer_name',
        'signer_position',
    ];

    protected $casts = [
        'content_layout' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function userCertificates()
    {
        return $this->hasMany(EventUserCertificate::class);
    }
}
