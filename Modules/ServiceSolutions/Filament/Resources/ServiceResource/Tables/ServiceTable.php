<?php

namespace Modules\ServiceSolutions\Filament\Resources\ServiceResource\Tables;

use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('icon')
                    ->square()
                    ->state(function ($record) {
                        $path = $record->icon;
                        if (!$path) return null;
                        if (str_starts_with($path, 'http')) return $path;
                        if (str_starts_with($path, 'assets') || str_starts_with($path, '/assets')) return url($path);
                        return url('storage/' . $path);
                    })
                    ->placeholder('-'),
                ImageColumn::make('thumbnail')
                    ->state(function ($record) {
                        $path = $record->thumbnail ?? $record->featured_image;
                        if (!$path) return null;
                        if (str_starts_with($path, 'http')) return $path;
                        if (str_starts_with($path, 'assets') || str_starts_with($path, '/assets')) return url($path);
                        return url('storage/' . $path);
                    })
                    ->placeholder('-'),
                ImageColumn::make('featured_image')
                    ->label('Background Detail')
                    ->state(function ($record) {
                        $path = $record->featured_image;
                        if (!$path) return null;
                        if (str_starts_with($path, 'http')) return $path;
                        if (str_starts_with($path, 'assets') || str_starts_with($path, '/assets')) return url($path);
                        return url('storage/' . $path);
                    })
                    ->placeholder('-')
                    ->visibility('public'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc');
    }
}
