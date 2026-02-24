<?php

namespace App\Filament\Exports;

use Modules\Core\Models\Brand;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class BrandExporter extends Exporter
{
    protected static ?string $model = Brand::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('name'),
            ExportColumn::make('slug')->label('slug'),
            ExportColumn::make('image')
                ->label('image')
                ->state(fn (Brand $record): ?string => $record->image
                    ? asset(\Illuminate\Support\Facades\Storage::url($record->image))
                    : null),
            ExportColumn::make('website_url')->label('website_url'),
            ExportColumn::make('desc')->label('desc'),
            ExportColumn::make('category')->label('category'),
            ExportColumn::make('is_featured')
                ->label('is_featured')
                ->state(fn (Brand $record): string => $record->is_featured ? '1' : '0'),

            // Hero Configuration (from landing_config JSON)
            ExportColumn::make('hero_eyebrow')
                ->label('hero_eyebrow')
                ->state(fn (Brand $record): ?string => $record->landing_config['hero']['eyebrow'] ?? null),
            ExportColumn::make('hero_title')
                ->label('hero_title')
                ->state(fn (Brand $record): ?string => $record->landing_config['hero']['title'] ?? null),
            ExportColumn::make('hero_subtitle')
                ->label('hero_subtitle')
                ->state(fn (Brand $record): ?string => $record->landing_config['hero']['subtitle'] ?? null),
            ExportColumn::make('hero_desc')
                ->label('hero_desc')
                ->state(fn (Brand $record): ?string => $record->landing_config['hero']['desc'] ?? null),
            ExportColumn::make('hero_cta_label')
                ->label('hero_cta_label')
                ->state(fn (Brand $record): ?string => $record->landing_config['hero']['cta_label'] ?? null),
            ExportColumn::make('hero_cta_url')
                ->label('hero_cta_url')
                ->state(fn (Brand $record): ?string => $record->landing_config['hero']['cta_url'] ?? null),

            // SEO Meta fields
            ExportColumn::make('seo.title')->label('seo_title'),
            ExportColumn::make('seo.description')->label('seo_description'),
            ExportColumn::make('seo.keywords')
                ->label('seo_keywords')
                ->state(fn (Brand $record): ?string => $record->seo?->keywords
                    ? implode(', ', $record->seo->keywords)
                    : null),
            ExportColumn::make('seo.og_title')->label('og_title'),
            ExportColumn::make('seo.og_description')->label('og_description'),
            ExportColumn::make('og_image')
                ->label('OG Image')
                ->state(fn (Brand $record): ?string => $record->seo?->og_image
                    ? asset(\Illuminate\Support\Facades\Storage::url($record->seo->og_image))
                    : null),
            ExportColumn::make('seo.canonical_url')->label('canonical_url'),
            ExportColumn::make('noindex')
                ->label('noindex')
                ->state(fn (Brand $record): string => $record->seo?->noindex ? '1' : '0'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export brand selesai. ' . number_format($export->successful_rows) . ' baris berhasil diexport.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diexport.';
        }

        return $body;
    }
}
