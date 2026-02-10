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
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('clients/telkom-indonesia.png'),
            ImportColumn::make('website_url')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->example('https://www.telkom.co.id'),
            ImportColumn::make('category')
                ->example('Telecommunication, BUMN'),
            ImportColumn::make('is_active')
                ->example(true), // Removed boolean()/rules() to handle empty strings
            ImportColumn::make('position')
                ->example(1), // Removed numeric()/rules()
            ImportColumn::make('seo_title')
                ->label('SEO Title')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:255'])
                ->example('PT Telkom Indonesia (Persero) Tbk - Digital Telco Company'),
            ImportColumn::make('seo_description')
                ->label('SEO Description')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:500'])
                ->example('Telkom Indonesia adalah BUMN yang bergerak di bidang jasa layanan teknologi informasi dan komunikasi (TIK) dan jaringan telekomunikasi di Indonesia.'),
            ImportColumn::make('seo_keywords')
                ->label('SEO Keywords')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->helperText('Comma separated keywords')
                ->example('telkom, indonesia, bumn, tik, telekomunikasi, digital, telco'),
            ImportColumn::make('og_title')
                ->label('OG Title')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:255'])
                ->example('Project Alpha - Best IT Service'),
            ImportColumn::make('og_description')
                ->label('OG Description')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:255'])
                ->example('Telkom Indonesia solusinya digital masa depan.'),
            ImportColumn::make('og_image')
                ->label('OG Image')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->example('clients/telkom-og.jpg'),
            ImportColumn::make('canonical_url')
                ->label('Canonical URL')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
                ->fillRecordUsing(fn ($record, $state) => null)
                ->rules(['nullable', 'max:255'])
                ->example('https://activ.co.id/clients/pt-telkom-indonesia-persero-tbk'),
            ImportColumn::make('noindex')
                ->label('No Index')
                ->castStateUsing(fn ($state) => blank($state) ? null : $state)
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
        \Log::info('Importing Client ID: ' . $this->record->id . ' | Slug: ' . $this->record->slug);
        \Log::info('Raw Data for SEO:', $this->data);

        $seoKeywords = $this->data['seo_keywords'] ?? null;
        $seoKeywords = blank($seoKeywords) ? null : array_map('trim', explode(',', $seoKeywords));

        $seoData = [
            'title' => blank($this->data['seo_title'] ?? null) ? null : $this->data['seo_title'],
            'description' => blank($this->data['seo_description'] ?? null) ? null : $this->data['seo_description'],
            'keywords' => $seoKeywords,
            'og_title' => blank($this->data['og_title'] ?? null) ? null : $this->data['og_title'],
            'og_description' => blank($this->data['og_description'] ?? null) ? null : $this->data['og_description'],
            'og_image' => blank($this->data['og_image'] ?? null) ? null : $this->data['og_image'],
            'canonical_url' => blank($this->data['canonical_url'] ?? null) ? null : $this->data['canonical_url'],
            'noindex' => blank($this->data['noindex'] ?? null) ? false : (bool) $this->data['noindex'],
        ];

        $this->record->seo()->updateOrCreate(
            ['seoable_id' => $this->record->id, 'seoable_type' => get_class($this->record)],
            $seoData
        );
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
