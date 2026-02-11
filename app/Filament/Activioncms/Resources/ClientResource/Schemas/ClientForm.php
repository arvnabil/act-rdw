<?php

namespace App\Filament\Activioncms\Resources\ClientResource\Schemas;

use App\Models\Client;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use App\Filament\Activioncms\Resources\SeoMetaResource\Schemas\SeoForm;
use Filament\Schemas\Components\Group;
use Illuminate\Support\Str;

class ClientForm
{
    public static function schema(): array
    {
        return [
            Tabs::make('Tabs')
                ->tabs([
                    Tabs\Tab::make('General')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->live(onBlur: true),

                            FileUpload::make('logo')
                                ->image()
                                ->disk('public')
                                ->visibility('public')
                                ->maxSize(2048)
                                ->preserveFilenames()
                                ->downloadable()
                                ->openable()
                                ->helperText('Nama file akan otomatis disesuaikan (contoh: telkom-indonesia.png). Ukuran maks: 2MB.')
                                ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, \Filament\Schemas\Components\Utilities\Get $get): string {
                                    $slug = Str::slug($get('name')) ?: 'temp';
                                    return \App\Helpers\UploadHelper::getSluggedFilename($file, 'clients/' . $slug);
                                })
                                ->required(),

                            TextInput::make('website_url')
                                ->url(),

                            TextInput::make('category')
                                ->label('Category')
                                ->placeholder('e.g. Telecommunication, Security')
                                ->helperText('Digunakan untuk pengelompokan di halaman Klien.'),

                            TextInput::make('position')
                                ->numeric()
                                ->default(0),

                            Toggle::make('is_active')
                                ->default(true),
                        ]),
                    Tabs\Tab::make('SEO')
                        ->schema([
                            Group::make()
                                ->relationship('seo')
                                ->schema(SeoForm::schema())
                        ]),
                ])->columnSpanFull()
        ];
    }
}
