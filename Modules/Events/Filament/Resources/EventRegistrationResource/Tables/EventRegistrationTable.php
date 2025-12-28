<?php

namespace Modules\Events\Filament\Resources\EventRegistrationResource\Tables;

use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class EventRegistrationTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Joined' => 'success',
                        'Registered' => 'success',
                        'Certified' => 'success',
                        'Rejected' => 'danger',
                        'Cancelled' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ticket_code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('event')
                    ->relationship('event', 'title'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
