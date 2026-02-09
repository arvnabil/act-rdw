<?php

namespace App\Filament\Activioncms\Resources\NewsResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NewsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail')
                    ->label('Featured image')
                    ->disk('public')
                    ->defaultImageUrl(url('/assets/img/logo/logo.png'))
                    ->width(160)
                    ->height(90)
                    ->extraImgAttributes(['style' => 'object-fit: contain; background: #222;']),
                TextColumn::make('title')->searchable()->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                    ]),
                TextColumn::make('published_at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
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
