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
            ImportColumn::make('id')
                ->label('id')
                ->numeric()
                ->rules(['nullable', 'integer'])
                ->example(1),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('PT Telkom Indonesia (Persero) Tbk'),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('pt-telkom-indonesia-persero-tbk'),
            ImportColumn::make('logo')
                ->label('Logo (URL or path)')
                ->fillRecordUsing(fn() => null)
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
        \Log::info('ClientImporter: afterSave started for Record Service: ' . $this->record->id);
        \Log::info('ClientImporter: Record current logo in DB: ' . $this->record->logo);

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

        // Handle external logo download
        $logo = isset($this->data['logo']) ? trim($this->data['logo']) : null;
        \Log::info('ClientImporter: Logo value from CSV: ' . ($logo ?: 'NULL'));

        if (!blank($logo) && (filter_var($logo, FILTER_VALIDATE_URL) || str_starts_with($logo, 'http'))) {
            // 1. Check if it's already a local URL
            $localPath = \App\Helpers\ImageHelper::getLocalPathFromUrl($logo);
            
            if ($localPath) {
                \Log::info("ClientImporter: Detected local URL '{$logo}', skipping download and using existing file: {$localPath}");
                $this->record->update(['logo' => $localPath]);
            } else {
                \Log::info("ClientImporter: Detected external URL '{$logo}', starting migration to local storage.");
                try {
                    // Robust URL handling: encode spaces
                    $cleanUrl = str_replace(' ', '%20', $logo);
                    
                    $response = \Illuminate\Support\Facades\Http::withoutVerifying()
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
                            'Accept' => 'image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8'
                        ])
                        ->timeout(60)
                        ->retry(3, 1000)
                        ->get($cleanUrl);

                    \Log::info("ClientImporter: Download response status: " . $response->status());

                    if ($response->successful()) {
                        $contents = $response->body();
                        \Log::info("ClientImporter: Download successful, size: " . strlen($contents) . " bytes");
                        
                        $filename = \Illuminate\Support\Str::slug($this->record->name ?: 'client') . '-' . time();
                        $targetPath = 'clients/' . $filename;
                        
                        $savedPath = \App\Helpers\ImageHelper::processAndConvert($contents, $targetPath);
                        if ($savedPath) {
                            \Log::info("ClientImporter: Image processed and saved to: {$savedPath}");
                            $this->record->update(['logo' => $savedPath]);
                            \Log::info("ClientImporter: Record updated with new logo path.");
                        } else {
                            \Log::error("ClientImporter: ImageHelper returned NULL after processing.");
                        }
                    } else {
                        \Log::warning('ClientImporter: URL download failed. Status: ' . $response->status() . ' URL: ' . $logo);
                    }
                } catch (\Throwable $e) {
                    \Log::error('ClientImporter: Failed to download/process logo: ' . $e->getMessage());
                }
            }
        } else {
            \Log::info("ClientImporter: Logo is either blank or NOT a URL. Skipping download.");
        }
    }

    public function resolveRecord(): Client
    {
        if ($this->data['id'] ?? null) {
            return Client::findOrNew($this->data['id']);
        }

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
