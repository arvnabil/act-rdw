<?php

namespace Modules\News\Filament\Exports;

use Modules\News\Models\News;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

use Illuminate\Support\Facades\Storage;

class NewsExporter extends Exporter
{
    protected static ?string $model = News::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('id'),

            ExportColumn::make('title')
                ->label('title'),

            ExportColumn::make('slug')
                ->label('slug'),

            ExportColumn::make('excerpt')
                ->label('excerpt'),

            ExportColumn::make('content')
                ->label('content'),

            ExportColumn::make('status')
                ->label('status'),

            ExportColumn::make('published_at')
                ->label('published_at')
                ->state(fn (News $record): ?string => $record->published_at?->format('n/j/Y H:i')),

            ExportColumn::make('thumbnail')
                ->label('thumbnail')
                ->state(fn (News $record): ?string => $record->thumbnail
                    ? asset(Storage::url($record->thumbnail))
                    : null),

            ExportColumn::make('categories')
                ->label('categories')
                ->state(fn (News $record): ?string => $record->categories->pluck('name')->implode(', ') ?: null),

            ExportColumn::make('tags')
                ->label('tags')
                ->state(fn (News $record): ?string => $record->tags->pluck('name')->implode(', ') ?: null),

            // SEO Meta fields
            ExportColumn::make('seo.title')
                ->label('seo_title'),

            ExportColumn::make('seo.description')
                ->label('seo_description'),

            ExportColumn::make('seo.keywords')
                ->label('seo_keywords')
                ->state(fn (News $record): ?string => $record->seo?->keywords
                    ? implode(', ', (array) $record->seo->keywords)
                    : null),

            ExportColumn::make('seo.og_title')
                ->label('og_title'),

            ExportColumn::make('seo.og_description')
                ->label('og_description'),

            ExportColumn::make('seo.og_image')
                ->label('og_image')
                ->state(fn (News $record): ?string => $record->seo?->og_image
                    ? asset(Storage::url($record->seo->og_image))
                    : null),

            ExportColumn::make('seo.canonical_url')
                ->label('canonical_url'),

            ExportColumn::make('seo.noindex')
                ->label('noindex')
                ->state(fn (News $record): string => $record->seo?->noindex ? '1' : '0'),
        ];
    }

    /**
     * Form fields shown inside the Export modal,
     * allowing users to narrow down what to export.
     */
    public static function getOptionsFormComponents(): array
    {
        return [
            \Filament\Schemas\Components\Section::make('Filter Export')
                ->description('Leave empty to export all news.')
                ->schema([
                    \Filament\Forms\Components\Select::make('published_month')
                        ->label('Published Month')
                        ->placeholder('All months')
                        ->options(function () {
                            return \Modules\News\Models\News::query()
                                ->whereNotNull('published_at')
                                ->pluck('published_at')
                                ->mapWithKeys(function ($date) {
                                    $carbon = \Carbon\Carbon::parse($date);
                                    return [$carbon->format('Y-m') => $carbon->format('F Y')];
                                })
                                ->sortKeysDesc()
                                ->toArray();
                        }),


                    \Filament\Forms\Components\Select::make('status_filter')
                        ->label('Status')
                        ->placeholder('All statuses')
                        ->options([
                            'draft'     => 'Draft',
                            'published' => 'Published',
                        ]),
                ]),
        ];
    }


    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your news export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
