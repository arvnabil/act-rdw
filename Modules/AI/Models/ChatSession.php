<?php

namespace Modules\AI\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    protected $table = 'ai_chat_sessions';

    protected $fillable = [
        'name',
        'whatsapp',
        'email',
        'company',
        'persona',
        'summary',
        'status',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'session_id');
    }
}
