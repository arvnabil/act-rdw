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
                ->visibility('public')
                ->maxSize(2048)
                ->downloadable()
                ->openable()
                ->helperText('Ukuran maks: 2MB.')
                ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, $get): string {
                    $eventId = $get('event_id');
                    $slug = $eventId ? \Modules\Events\Models\Event::find($eventId)?->slug : 'event';
                    return \App\Helpers\UploadHelper::getSluggedFilename($file, 'events/' . $slug . '/documentations');
                })
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
