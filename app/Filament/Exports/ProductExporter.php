<?php

namespace App\Filament\Exports;

use Modules\Core\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('id'),
            ExportColumn::make('name')->label('name'),
            ExportColumn::make('slug')->label('slug'),
            ExportColumn::make('sku')->label('sku'),
            ExportColumn::make('price')->label('price'),
            ExportColumn::make('is_active')->label('is_active')->state(fn (Product $record): string => $record->is_active ? 'yes' : 'no'),
            ExportColumn::make('is_featured')->label('is_featured')->state(fn (Product $record): string => $record->is_featured ? 'yes' : 'no'),
            ExportColumn::make('brand.name')->label('brand_name'),
            ExportColumn::make('service.name')->label('service_name'),
            ExportColumn::make('categories')->label('category_name')->state(function (Product $record) {
                return $record->categories->pluck('name')->join(', ');
            }),
            ExportColumn::make('solutions')->label('solutions')->state(function (Product $record) {
                return $record->solutions->pluck('title')->join(', ');
            }),
            ExportColumn::make('link_accommerce')->label('link_accommerce'),
            ExportColumn::make('whatsapp_note')->label('whatsapp_note'),
            ExportColumn::make('description')->label('description'),
            ExportColumn::make('datasheet_url')->label('datasheet_url'),
            ExportColumn::make('created_at')->label('created_at'),
            ExportColumn::make('updated_at')->label('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Product export completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
