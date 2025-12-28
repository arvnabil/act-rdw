<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Organizer extends Model
{
    use HasFactory;
    use \Modules\Events\Traits\HasEventFileManagement;

    protected function fileFields(): array
    {
        return [
            'logo' => 'logo',
        ];
    }

    protected function getStorageBasePath(): string
    {
        return "events/organizer";
    }

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'logo',
        'description',
        'website',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function users()
    {
        return $this->belongsToMany(EventUser::class, 'event_user_organizer');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('name') && empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }
}
