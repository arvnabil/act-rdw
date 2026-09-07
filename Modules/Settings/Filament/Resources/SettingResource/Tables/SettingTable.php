<?php

namespace Modules\Settings\Filament\Resources\SettingResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Settings\Models\Setting;

class SettingTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->limit(50)
                    ->formatStateUsing(function ($state, Setting $record): string {
                        // Never render the ciphertext (or plaintext) of secret
                        // settings in the admin table.
                        if (Setting::isSecretKey($record->key)) {
                            return filled((string) $state) ? '•••••••••••• (configured)' : '—';
                        }

                        return (string) $state;
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
