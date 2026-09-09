<?php

namespace Modules\Campaign\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class Campaign extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'content_json' => 'array',
        'seo_data'     => 'array',
        'geo_data'     => 'array',
        'starts_at'    => 'datetime',
        'ends_at'      => 'datetime',
        'published_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function ctas()
    {
        return $this->hasMany(CampaignCta::class)->orderBy('position');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /** Only campaigns with status = published. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /** Published campaigns within their schedule window (if set). */
    public function scopeActive(Builder $query): Builder
    {
        $now = Carbon::now();

        return $query->published()->where(function (Builder $q) use ($now) {
            $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
        })->where(function (Builder $q) use ($now) {
            $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
        });
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isPubliclyVisible(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        $now = Carbon::now();

        if ($this->starts_at && $this->starts_at->gt($now)) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->lt($now)) {
            return false;
        }

        return true;
    }

    /**
     * Always return geo_data with default structure when null.
     */
    public function getGeoDataAttribute($value): array
    {
        $default = [
            'entity'               => ['name' => '', 'type' => ''],
            'summary'              => '',
            'key_facts'            => [],
            'benefits'             => [],
            'use_cases'            => [],
            'faq'                  => [],
            'organization_context' => '',
        ];

        if (empty($value)) {
            return $default;
        }

        $decoded = is_array($value) ? $value : json_decode($value, true);

        return array_merge($default, $decoded ?? []);
    }
}