<?php

namespace Modules\Services\Filament\Exports;

use Modules\Services\Models\ServiceSolution;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ServiceSolutionExporter extends Exporter
{
    protected static ?string $model = ServiceSolution::class;

    public static function getColumns(): array
    {
        return [
            // Relationship
            ExportColumn::make('service.name')->label('service_name'),

            // Basic fields
            ExportColumn::make('title')->label('title'),
            ExportColumn::make('slug')->label('slug'),
            ExportColumn::make('subtitle')->label('subtitle'),
            ExportColumn::make('description')->label('description'),
            ExportColumn::make('wa_message')->label('wa_message'),
            ExportColumn::make('configurator_slug')->label('configurator_slug'),
            ExportColumn::make('sort_order')->label('sort_order'),

            // Categories (M2M, comma separated)
            ExportColumn::make('category_names')
                ->label('category_names')
                ->state(fn (ServiceSolution $record): ?string =>
                    $record->categories->pluck('label')->join(', ') ?: null),

            // SEO Fields
            ExportColumn::make('seo.title')->label('seo_title'),
            ExportColumn::make('seo.description')->label('seo_description'),
            ExportColumn::make('seo.keywords')
                ->label('seo_keywords')
                ->state(fn (ServiceSolution $record): ?string => $record->seo?->keywords
                    ? (is_array($record->seo->keywords) ? implode(', ', $record->seo->keywords) : $record->seo->keywords)
                    : null),
            ExportColumn::make('seo.og_title')->label('og_title'),
            ExportColumn::make('seo.og_description')->label('og_description'),
            ExportColumn::make('og_image')
                ->label('OG Image')
                ->state(fn (ServiceSolution $record): ?string => $record->seo?->og_image
                    ? asset(\Illuminate\Support\Facades\Storage::url($record->seo->og_image))
                    : null),
            ExportColumn::make('seo.canonical_url')->label('canonical_url'),
            ExportColumn::make('noindex')
                ->label('noindex')
                ->state(fn (ServiceSolution $record): string => $record->seo?->noindex ? '1' : '0'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export solusi service selesai. ' . number_format($export->successful_rows) . ' baris berhasil diexport.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diexport.';
        }

        return $body;
    }
}
