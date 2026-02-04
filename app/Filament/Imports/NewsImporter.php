<?php

namespace App\Filament\Imports;

use App\Models\News;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class NewsImporter extends Importer
{
    protected static ?string $model = News::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('excerpt'),
            ImportColumn::make('content'),
            ImportColumn::make('status')
                ->rules(['in:draft,published']),
            ImportColumn::make('published_at')
                ->rules(['datetime']),
            ImportColumn::make('categories')
                ->fillRecordUsing(fn() => null)
                ->helperText('Comma separated category names'),
        ];
    }

    public function resolveRecord(): News
    {
        return News::firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    protected function afterSave(): void
    {
        if ($categoriesString = $this->data['categories'] ?? null) {
            $categoryNames = array_map('trim', explode(',', $categoriesString));
            $categoryIds = \App\Models\NewsCategory::whereIn('name', $categoryNames)
                ->pluck('id')
                ->toArray();
            
            $this->record->categories()->sync($categoryIds);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your news import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
