<?php

namespace Modules\Events\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'event_user_id',
        'name',
        'email',
        'phone',
        'amount',
        'status', // pending, paid, rejected, cancelled
        'payment_method',
        'payment_proof',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(EventUser::class);
    }

    public function certificate()
    {
        return $this->hasOne(EventCertificate::class);
    }
}
