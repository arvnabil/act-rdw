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
                ->visibility('public')
                ->maxSize(2048)
                ->downloadable()
                ->openable()
                ->helperText('Ukuran maks: 2MB.')
                ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, $get): string {
                    $eventId = $get('event_id');
                    $slug = $eventId ? \Modules\Events\Models\Event::find($eventId)?->slug : 'event';
                    return \App\Helpers\UploadHelper::getSluggedFilename($file, 'certificates/' . $slug . '/background');
                })
                ->preserveFilenames()
                ->required(),
            Forms\Components\FileUpload::make('image_files')
                ->label('Image Assets (Signatures, Logos, etc.)')
                ->image()
                ->multiple()
                ->disk('public')
                ->visibility('public')
                ->maxSize(2048)
                ->downloadable()
                ->openable()
                ->helperText('Ukuran maks: 2MB.')
                ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, $get): string {
                    $eventId = $get('event_id');
                    $slug = $eventId ? \Modules\Events\Models\Event::find($eventId)?->slug : 'event';
                    return \App\Helpers\UploadHelper::getSluggedFilename($file, 'certificates/' . $slug . '/assets');
                })
                ->columnSpanFull()
                ->formatStateUsing(fn ($state) => collect($state ?? [])
                    ->map(fn ($item) => is_array($item) && isset($item['path']) ? $item['path'] : $item)
                    ->toArray()),
            // json layout or other fields if needed
        ];
    }
}
