<?php

namespace Modules\Events\Filament\Resources\EventCertificateResource\Tables;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Events\Models\EventCertificate; // For type hinting in action action

class EventCertificateTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('certificate_background')
                    ->label('Background')
                    ->circular()
                    ->stacked(),
                Tables\Columns\TextColumn::make('event.title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('signer_name')
                    ->label('Signer Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('signer_position')
                    ->label('Signer Position')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
