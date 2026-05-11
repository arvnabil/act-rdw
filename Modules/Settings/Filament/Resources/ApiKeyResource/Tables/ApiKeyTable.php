<?php

namespace Modules\Settings\Filament\Resources\ApiKeyResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns;

class ApiKeyTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                Columns\TextColumn::make('key')
                    ->label('Key Pattern')
                    ->formatStateUsing(fn ($state) => substr($state, 0, 8) . '...')
                    ->copyable()
                    ->copyMessage('API Key copied to clipboard')
                    ->description(fn ($record) => "Click to copy full key"),

                Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                Columns\IconColumn::make('debug_mode')
                    ->boolean()
                    ->label('Debug'),

                Columns\TextColumn::make('last_used_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never used'),

                Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Permanent')
                    ->color(fn ($record) => $record->expires_at && $record->expires_at->isPast() ? 'danger' : 'success'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('debug_mode'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
