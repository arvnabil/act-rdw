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
                                ->preserveFilenames()
                                ->downloadable()
                                ->openable()
                                ->helperText('Nama file akan otomatis disesuaikan (contoh: telkom-indonesia.png).')
                                ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, \Filament\Schemas\Components\Utilities\Get $get): string {
                                    $slug = Str::slug($get('name')) ?: 'temp';
                                    $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                                    $extension = $file->getClientOriginalExtension();
                                    $basePath = 'clients/' . $slug . '/' . $filename . '.' . $extension;
                                    
                                    $counter = 1;
                                    $finalPath = $basePath;
                                    while (\Illuminate\Support\Facades\Storage::disk('public')->exists($finalPath)) {
                                        $finalPath = 'clients/' . $slug . '/' . $filename . '-(' . $counter . ').' . $extension;
                                        $counter++;
                                    }
                                    
                                    return $finalPath;
                                })
                                ->required(),

                            TextInput::make('website_url')
                                ->url(),

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
