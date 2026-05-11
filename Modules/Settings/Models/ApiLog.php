<?php

namespace Modules\Settings\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $fillable = [
        'api_key_id',
        'endpoint',
        'method',
        'status_code',
        'ip_address',
        'user_agent',
        'payload',
        'response',
        'duration_ms',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
        'duration_ms' => 'float',
    ];

    public function apiKey()
    {
        return $this->belongsTo(ApiKey::class);
    }
}
