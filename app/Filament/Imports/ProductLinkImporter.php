<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Modules\Core\Models\Product;

class ProductLinkImporter extends Importer
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'exists:products,slug'])
                ->example('yealink-mvc840'),
            
            ImportColumn::make('link_accommerce')
                ->label('Ecommerce Link')
                ->rules(['nullable', 'url'])
                ->example('https://tokopedia.com/...'),
                
            ImportColumn::make('whatsapp_note')
                ->label('WhatsApp Note')
                ->example('Halo admin, ready stok?'),
        ];
    }

    public function resolveRecord(): ?Product
    {
        // Strict matching by slug. We only want to UPDATE existing records.
        // We use first() instead of firstOrNew because we don't want to create new products here.
        return Product::where('slug', $this->data['slug'])->first();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import Link selesai! ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
