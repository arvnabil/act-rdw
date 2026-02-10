<?php

namespace App\Filament\Imports;

use App\Models\Client;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ClientImporter extends Importer
{
    protected static ?string $model = Client::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('PT Telkom Indonesia (Persero) Tbk'),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('pt-telkom-indonesia-persero-tbk'),
            ImportColumn::make('logo')
                ->example('clients/telkom-indonesia.png'),
            ImportColumn::make('website_url')
                ->example('https://www.telkom.co.id'),
            ImportColumn::make('is_active')
                ->example(true), // Removed boolean()/rules() to handle empty strings
            ImportColumn::make('position')
                ->example(1), // Removed numeric()/rules()
            ImportColumn::make('seo_title')
                ->label('SEO Title')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:60'])
                ->example('PT Telkom Indonesia (Persero) Tbk - Digital Telco Company'),
            ImportColumn::make('seo_description')
                ->label('SEO Description')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:160'])
                ->example('Telkom Indonesia adalah BUMN yang bergerak di bidang jasa layanan teknologi informasi dan komunikasi (TIK) dan jaringan telekomunikasi di Indonesia.'),
            ImportColumn::make('seo_keywords')
                ->label('SEO Keywords')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->helperText('Comma separated keywords')
                ->example('telkom, indonesia, bumn, tik, telekomunikasi, digital, telco'),
            ImportColumn::make('og_title')
                ->label('OG Title')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:255'])
                ->example('PT Telkom Indonesia (Persero) Tbk'),
            ImportColumn::make('og_description')
                ->label('OG Description')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:255'])
                ->example('Telkom Indonesia solusinya digital masa depan.'),
            ImportColumn::make('og_image')
                ->label('OG Image')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->example('clients/telkom-og.jpg'),
            ImportColumn::make('canonical_url')
                ->label('Canonical URL')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:255'])
                ->example('https://activ.co.id/clients/pt-telkom-indonesia-persero-tbk'),
            ImportColumn::make('noindex')
                ->label('No Index')
                ->fillRecordUsing(fn ($record, $state) => null)
                ->example(false), // Removed boolean()/rules()
        ];
    }

    protected function beforeSave(): void
    {
        // Handle defaults for boolean/numeric fields if blank
        if (blank($this->data['is_active'] ?? null)) {
            $this->record->is_active = true;
        }
        
        if (blank($this->data['position'] ?? null)) {
            $this->record->position = 0;
        }
    }

    protected function afterSave(): void
    {
        $seoKeywords = $this->data['seo_keywords'] ?? null;
        $seoKeywords = blank($seoKeywords) ? null : array_map('trim', explode(',', $seoKeywords));

        $seoData = [
            'title' => $this->data['seo_title'] ?? null,
            'description' => $this->data['seo_description'] ?? null,
            'keywords' => $seoKeywords,
            'og_title' => $this->data['og_title'] ?? null,
            'og_description' => $this->data['og_description'] ?? null,
            'og_image' => $this->data['og_image'] ?? null,
            'canonical_url' => $this->data['canonical_url'] ?? null,
            'noindex' => blank($this->data['noindex'] ?? null) ? false : (bool) $this->data['noindex'],
        ];

        // Filter out null values to avoid overwriting existing data with nulls if not intended,
        // BUT keep 'noindex' as it is handled above.
        // Actually, array_filter removes false values if no callback.
        // We use callback to remove only NULLs.
        $seoData = array_filter($seoData, fn($value) => !is_null($value));

        if (!empty($seoData)) {
            $this->record->seo()->updateOrCreate(
                ['seoable_id' => $this->record->id, 'seoable_type' => get_class($this->record)],
                $seoData
            );
        }
    }

    public function resolveRecord(): Client
    {
        return Client::firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Proses import data client selesai. ' . Number::format($import->successful_rows) . ' baris berhasil diimport.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' baris gagal diimport.';
        }

        return $body;
    }
}
