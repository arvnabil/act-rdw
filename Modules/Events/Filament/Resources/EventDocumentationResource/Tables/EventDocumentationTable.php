<?php

namespace Modules\Events\Filament\Resources\EventDocumentationResource\Tables;

use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class EventDocumentationTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event.title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge(),
                Tables\Columns\ImageColumn::make('file_path')
                    ->label('Preview')
                    ->checkFileExistence(false)
                    ->visibility(fn ($record) => $record && $record->type === 'image'),
                Tables\Columns\TextColumn::make('caption')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->relationship('event', 'title'),
                Tables\Filters\SelectFilter::make('type')
                     ->options([
                        'image' => 'Image',
                        'presentation' => 'Presentation',
                        'video_link' => 'Video Link',
                    ]),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
