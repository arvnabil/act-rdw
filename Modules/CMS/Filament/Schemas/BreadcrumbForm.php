<?php

namespace Modules\CMS\Filament\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;

class BreadcrumbForm
{
    public static function schema(string $directory = 'services'): array
    {
        return [
            Section::make('Breadcrumb Settings')
                ->schema([
                    Toggle::make('show_breadcrumb')
                        ->label('Show Breadcrumb')
                        ->default(true)
                        ->live(),

                    FileUpload::make('breadcrumb_image')
                        ->label('Breadcrumb Thumbnail')
                        ->image()
                        ->disk('public')
                        ->visibility('public')
                        ->maxSize(2048)
                        ->downloadable()
                        ->openable()
                        ->imageEditor()
                        ->helperText('Custom background for breadcrumb. Leave empty for default. (Max: 2MB)')
                        ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file, $get) use ($directory): string {
                            return \App\Helpers\UploadHelper::getSluggedFilename($file, $directory);
                        })
                        ->visible(fn ($get) => $get('show_breadcrumb')),
                ])
                ->collapsible()
        ];
    }
}
