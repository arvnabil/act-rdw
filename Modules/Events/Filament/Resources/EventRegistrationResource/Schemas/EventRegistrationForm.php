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
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'rejected' => 'Rejected',
                    'cancelled' => 'Cancelled',
                ])
                ->required(),
            Forms\Components\FileUpload::make('payment_proof')
                ->image()
                ->disk('public')
                ->directory('payment-proofs')
                ->visibility('public'),
        ];
    }
}
