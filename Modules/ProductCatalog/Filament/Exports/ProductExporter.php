<?php

namespace Modules\ProductCatalog\Filament\Exports;

use Modules\ProductCatalog\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use App\Helpers\JsonHelper;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            // Basic Info
            ExportColumn::make('name')->label('name'),
            ExportColumn::make('slug')->label('slug'),
            ExportColumn::make('sku')->label('sku'),
            ExportColumn::make('price')->label('price'),
            ExportColumn::make('description')->label('description'),
            ExportColumn::make('image_path')
                ->label('image_path')
                ->state(fn (Product $record): ?string => $record->image_path
                    ? asset(\Illuminate\Support\Facades\Storage::url($record->image_path))
                    : null),
            ExportColumn::make('datasheet_url')->label('datasheet_url'),

            // Specs & Features (JSON format matching import)
            ExportColumn::make('specs')
                ->label('specs')
                ->state(function (Product $record): ?string {
                    if (empty($record->specs)) return null;
                    // Flatten nested specs back to "Group - Key" format for re-import
                    $flattened = JsonHelper::flatten($record->specs);
                    return json_encode($flattened, JSON_UNESCAPED_UNICODE);
                }),
            ExportColumn::make('features')
                ->label('features')
                ->state(function (Product $record): ?string {
                    if (empty($record->features)) return null;
                    return json_encode($record->features, JSON_UNESCAPED_UNICODE);
                }),
            ExportColumn::make('tags')
                ->label('tags')
                ->state(function (Product $record): ?string {
                    if (empty($record->tags)) return null;
                    return implode(', ', $record->tags);
                }),
            ExportColumn::make('specification_text')->label('specification_text'),
            ExportColumn::make('features_text')->label('features_text'),

            // Relationships
            ExportColumn::make('brand.name')->label('brand_name'),
            ExportColumn::make('service.name')->label('service_name'),
            ExportColumn::make('categories')
                ->label('category_name')
                ->state(fn (Product $record): ?string => $record->categories->pluck('name')->join(', ') ?: null),
            ExportColumn::make('solutions')
                ->label('solutions')
                ->state(fn (Product $record): ?string => $record->solutions->pluck('title')->join(', ') ?: null),

            // Market Links
            ExportColumn::make('link_accommerce')->label('link_accommerce'),
            ExportColumn::make('whatsapp_note')->label('whatsapp_note'),

            // Status
            ExportColumn::make('is_active')
                ->label('is_active')
                ->state(fn (Product $record): string => $record->is_active ? 'yes' : 'no'),
            ExportColumn::make('is_featured')
                ->label('is_featured')
                ->state(fn (Product $record): string => $record->is_featured ? 'yes' : 'no'),

            // SEO Fields
            ExportColumn::make('seo.title')->label('seo_title'),
            ExportColumn::make('seo.description')->label('seo_description'),
            ExportColumn::make('seo.keywords')
                ->label('seo_keywords')
                ->state(function (Product $record): ?string {
                    $keywords = $record->seo?->keywords;
                    if (empty($keywords)) return null;
                    return is_array($keywords) ? implode(', ', $keywords) : $keywords;
                }),
            ExportColumn::make('seo.og_title')->label('og_title'),
            ExportColumn::make('seo.og_description')->label('og_description'),
            ExportColumn::make('og_image')
                ->label('OG Image')
                ->state(fn (Product $record): ?string => $record->seo?->og_image
                    ? asset(\Illuminate\Support\Facades\Storage::url($record->seo->og_image))
                    : null),
            ExportColumn::make('seo.canonical_url')->label('canonical_url'),
            ExportColumn::make('noindex')
                ->label('noindex')
                ->state(fn (Product $record): string => $record->seo?->noindex ? '1' : '0'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export produk selesai. ' . number_format($export->successful_rows) . ' baris berhasil diexport.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diexport.';
        }

        return $body;
    }
}
