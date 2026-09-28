<?php

namespace Modules\Settings\Filament\Resources\ApiKeyResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ApiKeyTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('key')
                    ->label('API Key')
                    ->formatStateUsing(fn ($state) => substr((string) $state, 0, 10) . '••••••••••••••••••••••••')
                    ->description('Klik teks untuk copy ke clipboard')
                    ->copyable()
                    ->copyableState(fn ($record) => $record->key)
                    ->copyMessage('API Key berhasil disalin ke clipboard!'),

                TextColumn::make('capabilities')
                    ->badge()
                    ->separator(',')
                    ->label('Capabilities'),

                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                TextColumn::make('last_used_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never used'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created At'),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
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