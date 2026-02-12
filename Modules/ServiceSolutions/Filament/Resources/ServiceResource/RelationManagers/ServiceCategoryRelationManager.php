<?php

namespace Modules\ServiceSolutions\Filament\Resources\ServiceResource\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup as ActionsBulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;

class ServiceCategoryRelationManager extends RelationManager
{
    protected static string $relationship = 'categories';
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                TextInput::make('value')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('.category-slug'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label'),
                TextColumn::make('value'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\ImportAction::make()
                    ->importer(\App\Filament\Imports\ServiceCategoryImporter::class)
                    ->modalDescription(fn () => new \Illuminate\Support\HtmlString('Download example CSV: <a href="#" wire:click.prevent="mountAction(\'downloadExample\')">Click here</a>')),
                \Filament\Actions\Action::make('downloadExample')
                    ->label('Download Example CSV')
                    ->hidden()
                    ->action(fn () => response()->download(public_path('examples/category-import.csv'))),
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DetachAction::make(),
            ])
            ->bulkActions([
                ActionsBulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
