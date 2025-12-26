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
                ->directory('certificates/backgrounds')
                ->visibility('public')
                ->required(),
            Forms\Components\FileUpload::make('signature')
                ->label('Signature Image')
                ->image()
                ->disk('public')
                ->directory('certificates/signatures')
                ->visibility('public')
                ->nullable(),
            Forms\Components\TextInput::make('signer_name')
                ->label('Signer Name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('signer_position')
                ->label('Signer Position')
                ->required()
                ->maxLength(255),
            // json layout or other fields if needed
        ];
    }
}
