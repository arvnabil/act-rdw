<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventUserCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_certificate_id',
        'event_id',
        'event_user_id',
        'certificate_code',
        'file_path',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function eventCertificate()
    {
        return $this->belongsTo(EventCertificate::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function eventUser()
    {
        return $this->belongsTo(EventUser::class);
    }
}
