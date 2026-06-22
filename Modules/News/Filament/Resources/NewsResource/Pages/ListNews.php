<?php

namespace Modules\News\Filament\Resources\NewsResource\Pages;

use Modules\News\Filament\Resources\NewsResource;
use Modules\News\Models\News;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Carbon\Carbon;

class ListNews extends ListRecords
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ExportAction::make()
                ->exporter(\Modules\News\Filament\Exports\NewsExporter::class)
                ->modifyQueryUsing(function ($query, array $options) {
                    // Start fresh to bypass any active table filters
                    $freshQuery = News::query();

                    if (!empty($options['published_month'])) {
                        try {
                            $startOfMonth = Carbon::createFromFormat('Y-m', $options['published_month'])->startOfMonth();
                            $endOfMonth = $startOfMonth->copy()->endOfMonth();
                            $freshQuery->whereBetween('published_at', [$startOfMonth, $endOfMonth]);
                        } catch (\Throwable $e) {}
                    }

                    if (!empty($options['status_filter'])) {
                        $freshQuery->where('status', $options['status_filter']);
                    }

                    return $freshQuery;
                }),
            Actions\ImportAction::make()
                ->importer(\Modules\News\Filament\Imports\NewsImporter::class),
        ];
    }
}
