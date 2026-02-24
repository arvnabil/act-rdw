<?php

namespace App\Filament\Exports;

use Modules\ServiceSolutions\Models\ServiceCategory;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ServiceCategoryExporter extends Exporter
{
    protected static ?string $model = ServiceCategory::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('service.name')->label('service_name'),
            ExportColumn::make('label')->label('label'),
            ExportColumn::make('value')->label('value'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export kategori service selesai. ' . number_format($export->successful_rows) . ' baris berhasil diexport.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diexport.';
        }

        return $body;
    }
}
