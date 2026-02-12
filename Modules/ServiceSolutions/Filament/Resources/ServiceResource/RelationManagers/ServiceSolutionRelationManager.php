<?php

namespace Modules\ServiceSolutions\Filament\Resources\ServiceResource\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup as ActionsBulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

class ServiceSolutionRelationManager extends RelationManager
{
    protected static string $relationship = 'solutions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                TextInput::make('slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),
                                TextInput::make('subtitle'),
                                Textarea::make('description')
                                    ->columnSpanFull(),
                                FileUpload::make('thumbnail')
                                    ->image()
                                    ->disk('public')
                                    ->visibility('public')
                                    ->maxSize(2048)
                                    ->downloadable()
                                    ->openable()
                                    ->helperText('Nama file akan otomatis disesuaikan (Contoh: nama-solution.png). Ukuran maks: 2MB.')
                                    ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, Get $get): string {
                                        $slug = $get('slug') ?: 'temp';
                                        return \App\Helpers\UploadHelper::getSluggedFilename($file, 'services/solutions/' . $slug);
                                    }),
                            ])->columns(2),

                        Tab::make('Features & Links')
                            ->schema([
                                Repeater::make('features')
                                    ->schema([
                                        FileUpload::make('icon')
                                            ->image()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->maxSize(2048)
                                            ->downloadable()
                                            ->openable()
                                            ->helperText('Ukuran maks: 2MB.')
                                            ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, Get $get): string {
                                                // Look up to the solution slug
                                                $solutionSlug = $get('../../slug') ?: 'feature';
                                                return \App\Helpers\UploadHelper::getSluggedFilename($file, 'services/features/' . $solutionSlug);
                                            }),
                                        TextInput::make('title')->required(),
                                        Textarea::make('text')->rows(2),
                                    ])->columns(2)->columnSpanFull(),

                                TextInput::make('wa_message')
                                    ->helperText('Custom text for WhatsApp consultation'),
                                TextInput::make('configurator_slug')
                                    ->placeholder('e.g. room-configuration'),

                                Select::make('categories')
                                    ->multiple()
                                    ->relationship('categories', 'label')
                                    ->preload(),

                                Select::make('brands')
                                    ->multiple()
                                    ->relationship('brands', 'name')
                                    ->preload(),
                            ])->columns(2),


                        Tab::make('SEO')
                            ->schema([
                                \Filament\Schemas\Components\Group::make()
                                    ->relationship('seo')
                                    ->schema(\App\Filament\Activioncms\Resources\SeoMetaResource\Schemas\SeoForm::schema())
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                ImageColumn::make('thumbnail')
                    ->square()
                    ->disk('public')
                    ->visibility('public'),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                \Filament\Actions\ImportAction::make()
                    ->importer(\App\Filament\Imports\ServiceSolutionImporter::class)
                    ->modalDescription(fn () => new \Illuminate\Support\HtmlString('Download example CSV: <a href="#" wire:click.prevent="mountAction(\'downloadExample\')">Click here</a>')),
                \Filament\Actions\Action::make('downloadExample')
                    ->label('Download Example CSV')
                    ->hidden()
                    ->action(fn () => response()->download(public_path('examples/solution-import.csv'))),
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
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }
}
