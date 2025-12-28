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
                    ->disk('public')
                    ->width(50),
                Tables\Columns\TextColumn::make('event.title')
                    ->sortable()
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
                Actions\Action::make('design')
                    ->label('Design')
                    ->icon('heroicon-o-paint-brush')
                    ->url(fn (EventCertificate $record) => route('events.certificates.design', $record->id))
                    ->openUrlInNewTab(),
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
