<?php

namespace App\Filament\Exports;

use Modules\ServiceSolutions\Models\Service;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ServiceExporter extends Exporter
{
    protected static ?string $model = Service::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('name'),
            ExportColumn::make('slug')->label('slug'),
            ExportColumn::make('description')->label('description'),
            ExportColumn::make('content')->label('content'),
            ExportColumn::make('excerpt')->label('excerpt'),
            ExportColumn::make('hero_subtitle')->label('hero_subtitle'),
            ExportColumn::make('grid_title')->label('grid_title'),
            ExportColumn::make('sort_order')->label('sort_order'),

            // SEO Fields
            ExportColumn::make('seo.title')->label('seo_title'),
            ExportColumn::make('seo.description')->label('seo_description'),
            ExportColumn::make('seo.keywords')
                ->label('seo_keywords')
                ->state(fn (Service $record): ?string => $record->seo?->keywords
                    ? (is_array($record->seo->keywords) ? implode(', ', $record->seo->keywords) : $record->seo->keywords)
                    : null),
            ExportColumn::make('seo.og_title')->label('og_title'),
            ExportColumn::make('seo.og_description')->label('og_description'),
            ExportColumn::make('og_image')
                ->label('OG Image')
                ->state(fn (Service $record): ?string => $record->seo?->og_image
                    ? asset(\Illuminate\Support\Facades\Storage::url($record->seo->og_image))
                    : null),
            ExportColumn::make('seo.canonical_url')->label('canonical_url'),
            ExportColumn::make('noindex')
                ->label('noindex')
                ->state(fn (Service $record): string => $record->seo?->noindex ? '1' : '0'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export service selesai. ' . number_format($export->successful_rows) . ' baris berhasil diexport.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diexport.';
        }

        return $body;
    }
}
