<?php

namespace Modules\SEO\Filament\Imports;

use Modules\SEO\Models\SeoWhitelistDomain;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class SeoWhitelistDomainImporter extends Importer
{
    protected static ?string $model = SeoWhitelistDomain::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('domain')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('description')
                ->rules(['nullable']),
            ImportColumn::make('is_active')
                ->boolean()
                ->rules(['required', 'boolean']),
        ];
    }

    public function resolveRecord(): ?SeoWhitelistDomain
    {
        return SeoWhitelistDomain::firstOrNew([
            'domain' => $this->data['domain'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your seo whitelist domain import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
