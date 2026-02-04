<?php

namespace App\Filament\Imports;

use App\Models\Project;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ProjectImporter extends Importer
{
    protected static ?string $model = Project::class;

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
            ImportColumn::make('is_featured')
                ->boolean(),
            ImportColumn::make('client'),
            ImportColumn::make('category'),
            ImportColumn::make('project_date')
                ->rules(['date']),
            ImportColumn::make('address'),
        ];
    }

    public function resolveRecord(): Project
    {
        return Project::firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your project import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
