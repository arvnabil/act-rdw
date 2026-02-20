<?php

namespace App\Filament\Exports;

use Modules\Core\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductLinkExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('slug')->label('slug'),
            ExportColumn::make('link_accommerce')->label('link_accommerce'),
            ExportColumn::make('whatsapp_note')->label('whatsapp_note'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Link export completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
