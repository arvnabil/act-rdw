<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnalyticsClickEvent extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'analytics_click_events';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'session_id',
        'event_type',
        'event_label',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
        'entity_type',
        'entity_id',
        'entity_slug',
        'cta_position',
        'ip_address',
        'city',
        'region',
        'country',
        'user_agent',
        'device',
        'referrer_url',
        'page_url',
        'target_value',
        'click_count',
        'is_bot',
        'is_converted',
        'lead_id',
        'deal_value',
        'meta',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'meta' => 'array',
        'is_bot' => 'boolean',
        'is_converted' => 'boolean',
        'deal_value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for WhatsApp events.
     */
    public function scopeWhatsApp($query)
    {
        return $query->where('event_type', 'whatsapp');
    }

    /**
     * Scope by UTM Source.
     */
    public function scopeBySource($query, $source)
    {
        return $query->where('utm_source', $source);
    }

    /**
     * Scope by Polymorphic Entity.
     */
    public function scopeByEntity($query, $type, $id)
    {
        return $query->where('entity_type', $type)->where('entity_id', $id);
    }

    /**
     * Scope for successfully converted leads.
     */
    public function scopeConverted($query)
    {
        return $query->where('is_converted', true);
    }

    /**
     * Get the parent entity (Product, Service, Project, Page, etc.)
     * This allows us to link an event to any model.
     */
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }
}
