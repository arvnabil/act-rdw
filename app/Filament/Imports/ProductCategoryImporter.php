<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Modules\Core\Models\ProductCategory;

class ProductCategoryImporter extends Importer
{
    protected static ?string $model = ProductCategory::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Video Conferencing'),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('video-conferencing'),
            ImportColumn::make('icon')
                ->example('heroicon-o-video-camera'),
            ImportColumn::make('sort_order')
                ->label('sort_order')
                ->rules([]) // Explicitly clear any rules to avoid ghost validation
                ->castStateUsing(fn ($state) => blank($state) ? 0 : (int) $state)
                ->example(1),
            ImportColumn::make('is_active')
                ->label('is_active')
                ->rules([]) // Explicitly clear any rules
                ->castStateUsing(fn ($state) => blank($state) ? true : (bool) $state)
                ->example(true),
        ];
    }

    public function resolveRecord(): ProductCategory
    {
        return ProductCategory::firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    protected function beforeSave(): void
    {
        // Handle default value for sort_order if blank
        if (blank($this->data['sort_order'] ?? null)) {
            $this->record->sort_order = 0;
        }

        // Handle default value for is_active if blank
        if (blank($this->data['is_active'] ?? null)) {
            $this->record->is_active = true;
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Proses import data kategori produk selesai. ' . Number::format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' baris gagal diimport.';
        }

        return $body;
    }
}
