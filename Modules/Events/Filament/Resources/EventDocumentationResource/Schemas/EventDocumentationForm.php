<?php

namespace Modules\Events\Filament\Resources\EventDocumentationResource\Schemas;

use Filament\Forms;


class EventDocumentationForm
{
    public static function schema(): array
    {
        return [
            Forms\Components\Select::make('event_id')
                ->relationship('event', 'title')
                ->required(),
            Forms\Components\Select::make('type')
                ->options([
                    'image' => 'Foto (Image)',
                    'pdf' => 'Materi (PDF/Presentation)',
                    'video_link' => 'Video Link (YouTube)',
                ])
                ->required()
                ->default('image')
                ->live(), // Reactive to hide/show fields
            Forms\Components\FileUpload::make('file_path')
                ->label('Upload Files')
                ->disk('public')
                ->directory(function ($get, ?\Illuminate\Database\Eloquent\Model $record) {
                    $slug = 'default';
                     if ($record && $record->event) {
                        $slug = $record->event->slug;
                    } elseif ($eventId = $get('event_id')) {
                        $event = \Modules\Events\Models\Event::find($eventId);
                        if ($event) {
                             $slug = $event->slug;
                        }
                    }
                    return "events/{$slug}/documentations";
                })
                ->visibility('public')
                ->maxSize(2048)
                ->preserveFilenames()
                ->multiple() // multiple upload
                ->reorderable()
                ->required(fn ($get) => $get('type') !== 'video_link')
                ->hidden(fn ($get) => $get('type') === 'video_link'),
            Forms\Components\TagsInput::make('file_path')
                ->label('Video Links (YouTube)')
                ->placeholder('Add link and press Enter')
                ->required(fn ($get) => $get('type') === 'video_link')
                ->hidden(fn ($get) => $get('type') !== 'video_link'),
            Forms\Components\Textarea::make('caption')
                ->maxLength(65535)
                ->columnSpanFull(),
        ];
    }
}
