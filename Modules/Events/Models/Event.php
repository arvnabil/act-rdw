<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\Events\Models\Organizer;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'start_date',
        'end_date',
        'location',
        'thumbnail',
        'is_active',
        'is_certificate_available',
        'event_category_id',
        'price',
        'quota',
        'organizer_id',
        'speakers',
        'map_url',
        'youtube_link',
        'meeting_link',
        'schedule',
        'faq',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'speakers' => 'array',
        'schedule' => 'array',
        'faq' => 'array',
        'is_certificate_available' => 'boolean',
    ];

    protected $appends = ['available_seats'];

    public function getAvailableSeatsAttribute()
    {
        $booked = $this->registrations()
            ->whereIn('status', ['pending', 'paid', 'approved', 'confirmed'])
            ->count();

        return max(0, $this->quota - $booked);
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function organizerInfo()
    {
        return $this->belongsTo(Organizer::class, 'organizer_id');
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function documentations()
    {
        return $this->hasMany(EventDocumentation::class);
    }

    public function certificates()
    {
        return $this->hasMany(EventCertificate::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('title') && empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }
}
