<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Modules\ServiceSolutions\Models\ServiceCategory;
use Modules\ServiceSolutions\Models\Service;
use Illuminate\Support\Str;

class ServiceCategoryImporter extends Importer
{
    protected static ?string $model = ServiceCategory::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('service_name')
                ->label('Service')
                ->rules(['required', 'string', 'max:255'])
                ->fillRecordUsing(function ($record, $state) {
                    $state = trim($state);
                    $slug = Str::slug($state);
                    $item = Service::firstOrCreate(
                        ['slug' => $slug],
                        ['name' => $state]
                    );
                    $record->service()->associate($item);
                })
                ->example('Communication'),
            ImportColumn::make('label')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Education'),
            ImportColumn::make('value')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('education'),
        ];
    }

    public function resolveRecord(): ServiceCategory
    {
        return ServiceCategory::firstOrNew([
            'value' => $this->data['value'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $successCount = Number::format($import->successful_rows);
        $failedCount = Number::format($import->getFailedRowsCount());

        $body = "Import Kategori Service selesai! {$successCount} baris berhasil diproses.";

        if ($failedCount > 0) {
            $body .= " Ada {$failedCount} baris yang gagal.";
        }

        return $body;
    }
}
