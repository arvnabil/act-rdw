<?php

namespace Modules\Events\Filament\Resources\EventRegistrationResource\Schemas;

use Filament\Forms;


class EventRegistrationForm
{
    public static function schema(): array
    {
        return [
            Forms\Components\Select::make('event_id')
                ->relationship('event', 'title')
                ->required(),
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('phone')
                ->tel()
                ->maxLength(255),
            Forms\Components\TextInput::make('amount')
                ->numeric()
                ->prefix('IDR')
                ->default(0),
            Forms\Components\TextInput::make('ticket_code')
                ->maxLength(255),
            Forms\Components\TextInput::make('invoice_number')
                ->maxLength(255),
            Forms\Components\TextInput::make('payment_method')
                ->maxLength(255),
            Forms\Components\Select::make('status')
                ->options([
                    'Pending' => 'Pending',
                    'Joined' => 'Joined',
                    'Registered' => 'Registered',
                    'Certified' => 'Certified',
                    'Rejected' => 'Rejected',
                    'Cancelled' => 'Cancelled',
                ])
                ->required(),
            Forms\Components\FileUpload::make('payment_proof')
                ->image()
                ->disk('public')
                ->visibility('public')
                ->maxSize(2048)
                ->downloadable()
                ->openable()
                ->helperText('Nama file akan otomatis disesuaikan (Contoh: nama-peserta.png). Ukuran maks: 2MB.')
                ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, $get): string {
                    $name = $get('name') ?: 'payment';
                    return \App\Helpers\UploadHelper::getSluggedFilename($file, 'event-registrations/' . $name);
                })
                ->preserveFilenames(),
        ];
    }
}
