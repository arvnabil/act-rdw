<?php

namespace App\Filament\Exports;

use App\Models\Client;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ClientExporter extends Exporter
{
    protected static ?string $model = Client::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('id'),
            ExportColumn::make('name')->label('name'),
            ExportColumn::make('slug')->label('slug'),
            ExportColumn::make('logo')
                ->label('logo')
                ->state(fn (Client $record): ?string => $record->logo ? asset(\Illuminate\Support\Facades\Storage::url($record->logo)) : null),
            ExportColumn::make('website_url')->label('website_url'),
            ExportColumn::make('category')->label('category'),
            ExportColumn::make('is_active')
                ->label('is_active')
                ->state(fn (Client $record): string => $record->is_active ? '1' : '0'),
            ExportColumn::make('position')->label('position'),
            
            // SEO Meta fields
            ExportColumn::make('seo.title')->label('seo_title'),
            ExportColumn::make('seo.description')->label('seo_description'),
            ExportColumn::make('seo.keywords')
                ->label('seo_keywords')
                ->state(fn (Client $record): ?string => $record->seo?->keywords ? implode(', ', $record->seo->keywords) : null),
            ExportColumn::make('seo.og_title')->label('og_title'),
            ExportColumn::make('seo.og_description')->label('og_description'),
            ExportColumn::make('og_image')
                ->label('OG Image')
                ->state(fn (Client $record): ?string => $record->seo?->og_image ? asset(\Illuminate\Support\Facades\Storage::url($record->seo->og_image)) : null),
            ExportColumn::make('canonical_url')->label('canonical_url'),
            ExportColumn::make('seo.noindex')
                ->label('noindex')
                ->state(fn (Client $record): string => $record->seo?->noindex ? '1' : '0'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Client export completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
