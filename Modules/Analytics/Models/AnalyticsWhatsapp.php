<?php

namespace Modules\Analytics\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsWhatsapp extends Model
{
    protected $table = 'analytics_whatsapp';

    protected $fillable = [
        'ip_address',
        'user_agent',
        'phone_number',
        'message',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'url_from',
        'country_code',
        'city',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
