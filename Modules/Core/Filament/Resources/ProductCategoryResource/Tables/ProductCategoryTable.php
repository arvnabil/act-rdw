<?php

namespace Modules\Core\Filament\Resources\ProductCategoryResource\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ProductCategoryTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('icon')
                    ->disk('public')
                    ->width(80)
                    ->height(80)
                    ->extraImgAttributes(['style' => 'object-fit: contain; background: #222;']),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug'),
                ToggleColumn::make('is_active'),
                TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Products')
            ])
            ->filters([
                //
            ])
            ->actions([
               EditAction::make(),
               DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
