<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Modules\ServiceSolutions\Models\Service;
use Illuminate\Support\Str;

class ServiceImporter extends Importer
{
    protected static ?string $model = Service::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('description')
                ->rules(['nullable']),
            ImportColumn::make('content')
                ->rules(['nullable']),
            ImportColumn::make('excerpt')
                ->rules(['nullable']),
            ImportColumn::make('hero_subtitle')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('grid_title')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('sort_order')
                ->numeric()
                ->rules(['nullable', 'integer']),
            
            // --- SEO Data (Service Only) ---
            ImportColumn::make('seo_title')
                ->label('SEO Title')
                ->fillRecordUsing(fn ($record) => null),
            ImportColumn::make('seo_description')
                ->label('SEO Description')
                ->fillRecordUsing(fn ($record) => null),
            ImportColumn::make('seo_keywords')
                ->label('SEO Keywords')
                ->fillRecordUsing(fn ($record) => null),
            ImportColumn::make('og_title')
                ->label('OG Title')
                ->fillRecordUsing(fn ($record) => null),
            ImportColumn::make('og_description')
                ->label('OG Description')
                ->fillRecordUsing(fn ($record) => null),
            ImportColumn::make('canonical_url')
                ->label('Canonical URL')
                ->fillRecordUsing(fn ($record) => null),
            ImportColumn::make('noindex')
                ->boolean()
                ->label('No Index')
                ->fillRecordUsing(fn ($record) => null),
        ];
    }

    public function resolveRecord(): Service
    {
        return Service::firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $data = $this->data;

        // Handle SEO Metadata
        if ($record->id) {
            $seoData = [
                'title' => $data['seo_title'] ?? $record->name,
                'description' => $data['seo_description'] ?? Str::limit(strip_tags($record->description), 160),
                'keywords' => $data['seo_keywords'] ?? null,
                'og_title' => $data['og_title'] ?? null,
                'og_description' => $data['og_description'] ?? null,
                'canonical_url' => $data['canonical_url'] ?? null,
                'noindex' => $data['noindex'] ?? false,
            ];

            $record->seo()->updateOrCreate(
                ['seoable_id' => $record->id, 'seoable_type' => get_class($record)],
                $seoData
            );
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import service selesai! ' . Number::format($import->successful_rows) . ' ' . str('baris')->plural($import->successful_rows) . ' berhasil diproses.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal.';
        }

        return $body;
    }
}
