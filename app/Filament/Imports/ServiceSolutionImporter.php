<?php

namespace App\Filament\Imports;

use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Modules\ServiceSolutions\Models\ServiceSolution;
use Modules\ServiceSolutions\Models\Service;
use Modules\ServiceSolutions\Models\ServiceCategory;
use Illuminate\Support\Str;
use App\Models\SeoMeta;

class ServiceSolutionImporter extends Importer
{
    protected static ?string $model = ServiceSolution::class;

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
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('Education Solution'),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:255'])
                ->example('education-solution'),
            ImportColumn::make('subtitle')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('description')
                ->rules(['nullable']),
            ImportColumn::make('wa_message')
                ->label('WA Message')
                ->rules(['nullable']),
            ImportColumn::make('configurator_slug')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('sort_order')
                ->numeric()
                ->rules(['nullable', 'integer']),
            
            // Categories Relationship (Comma Separated)
            ImportColumn::make('category_names')
                ->label('Categories (Separate by comma)')
                ->fillRecordUsing(fn ($record) => null)
                ->example('Education, Healthcare'),

            // SEO Data
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
            ImportColumn::make('og_image')
                ->label('OG Image')
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

    public function resolveRecord(): ServiceSolution
    {
        return ServiceSolution::firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $data = $this->data;

        // 1. Handle Categories
        if (!empty($data['category_names'])) {
            $categoryLabels = array_map('trim', explode(',', $data['category_names']));
            $categoryIds = [];
            foreach ($categoryLabels as $label) {
                $category = ServiceCategory::firstOrCreate(
                    ['label' => $label, 'service_id' => $record->service_id],
                    ['value' => Str::slug($label)]
                );
                $categoryIds[] = $category->id;
            }
            $record->categories()->sync($categoryIds);
        }

        // 2. Handle SEO Metadata
        if ($record->id) {
            $seoData = [
                'title' => $data['seo_title'] ?? $record->title,
                'description' => $data['seo_description'] ?? Str::limit(strip_tags($record->description), 160),
                'keywords' => $data['seo_keywords'] ?? null,
                'og_title' => $data['og_title'] ?? null,
                'og_description' => $data['og_description'] ?? null,
                'og_image' => $data['og_image'] ?? $record->thumbnail,
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
        $successCount = Number::format($import->successful_rows);
        $failedCount = Number::format($import->getFailedRowsCount());

        $body = "Import Solusi Service selesai! {$successCount} baris berhasil diproses.";

        if ($failedCount > 0) {
            $body .= " Ada {$failedCount} baris yang gagal.";
        }

        return $body;
    }
}
