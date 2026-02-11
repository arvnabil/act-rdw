<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasImageCleanup;

class EventUser extends Authenticatable
{
    use HasFactory, Notifiable, HasImageCleanup;

    protected $cleanupFields = ['avatar'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function organizers()
    {
        return $this->belongsToMany(Organizer::class, 'event_user_organizer');
    }
}
