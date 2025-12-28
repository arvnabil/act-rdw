<?php

namespace Modules\Events\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventDocumentation extends Model
{
    use HasFactory;
    use \Modules\Events\Traits\HasEventFileManagement;

    protected function fileFields(): array
    {
        return [
            'file_path' => 'documentations',
        ];
    }

    protected function getStorageBasePath(): string
    {
        // Safety check if event relationship is loaded or exists
        $slug = $this->event->slug ?? 'default';
        return "events/{$slug}/documentations";
    }

    protected $fillable = [
        'event_id',
        'type', // image, presentation
        'file_path',
        'caption',
    ];

    protected $casts = [
        'file_path' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
