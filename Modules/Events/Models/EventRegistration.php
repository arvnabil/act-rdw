<?php

namespace Modules\Events\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasImageCleanup;

class EventRegistration extends Model
{
    use HasImageCleanup, HasFactory;

    protected $cleanupFields = ['payment_proof'];

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
        'invoice_number',
        'ticket_code',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(EventUser::class, 'event_user_id');
    }

    public function certificate()
    {
        return $this->hasOne(EventCertificate::class);
    }
}
