<?php

namespace Modules\Events\Filament\Resources\EventCertificateResource\Schemas;

use Filament\Forms;

class EventCertificateForm
{
    public static function schema(): array
    {
        return [
            Forms\Components\Select::make('event_id')
                ->relationship('event', 'title')
                ->required()
                ->reactive(),
            Forms\Components\FileUpload::make('certificate_background')
                ->label('Background Image')
                ->image()
                ->disk('public')
                ->directory(function ($get) {
                    $eventId = $get('event_id');
                    $slug = $eventId ? \Modules\Events\Models\Event::find($eventId)?->slug : 'default';
                    return "certificates/backgrounds/{$slug}";
                })
                ->preserveFilenames()
                ->maxSize(4096)
                ->visibility('public')
                ->required(),
            Forms\Components\FileUpload::make('image_files')
                ->label('Image Assets (Signatures, Logos, etc.)')
                ->image()
                ->multiple()
                ->disk('public')
                ->directory(function ($get) {
                    $eventId = $get('event_id');
                    $slug = $eventId ? \Modules\Events\Models\Event::find($eventId)?->slug : 'default';
                    return "certificates/assets/{$slug}";
                })
                ->maxSize(2048)
                ->visibility('public')
                ->columnSpanFull()
                ->formatStateUsing(fn ($state) => collect($state ?? [])
                    ->map(fn ($item) => is_array($item) && isset($item['path']) ? $item['path'] : $item)
                    ->toArray()),
            // json layout or other fields if needed
        ];
    }
}
