<?php

namespace Modules\Settings\Filament\Resources\ApiKeyResource\Tables;

use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
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
                    ->label('Key Prefix')
                    ->formatStateUsing(fn ($state) => substr($state, 0, 8) . '************************')
                    ->description('Full key is hidden for security'),

                Columns\TextColumn::make('capabilities')
                    ->badge()
                    ->separator(',')
                    ->label('Capabilities'),

                Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),

                Columns\TextColumn::make('last_used_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Never used'),

                Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created At'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
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