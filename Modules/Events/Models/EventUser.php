<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class EventUser extends Authenticatable
{
    use HasFactory, Notifiable;
    use \Modules\Events\Traits\HasEventFileManagement;

    protected function fileFields(): array
    {
        return [
            'avatar' => 'avatar',
        ];
    }

    protected function getStorageBasePath(): string
    {
        // EventUser is global, not tied to single event. 
        // Using generic 'users' directory instead of specific event slug.
        return "events/users";
    }

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
