<?php

namespace Modules\Core\Filament\Resources\ProductCategoryResource\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

class ProductCategoryForm
{
    public static function schema(): array
    {
        return [
            Section::make('Category Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true),
                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                    Toggle::make('is_active')
                        ->default(true),
                    FileUpload::make('icon')
                        ->image()
                        ->disk('public')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->downloadable()
                        ->openable()
                        ->helperText('Nama file akan otomatis disesuaikan (Contoh: nama-produk.png). Ukuran maks: 2MB.')
                        ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, Get $get): string {
                            $slug = $get('slug') ?: 'temp';
                            return \App\Helpers\UploadHelper::getSluggedFilename($file, 'product-categories/' . $slug);
                        })
                ])->columns(2)
        ];
    }
}
