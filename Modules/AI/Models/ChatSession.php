<?php

namespace Modules\AI\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('ai_chat_sessions')]
#[Fillable(['name', 'whatsapp', 'email', 'company', 'persona', 'summary', 'status'])]
class ChatSession extends Model
{

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'session_id');
    }
}
