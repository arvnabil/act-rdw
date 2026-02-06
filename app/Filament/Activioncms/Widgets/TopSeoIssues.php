<?php

namespace App\Filament\Activioncms\Widgets;

use App\Models\SeoMeta;
use App\Filament\Activioncms\Resources\SeoMetaResource;
use App\Services\Seo\SeoAuditService;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;

class TopSeoIssues extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static bool $shouldDisplayOnDashboard = false;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Top SEO Issues';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SeoMeta::query()
                    ->with('seoable')
                    ->addSelect(DB::raw('*, (100
                        - (CASE WHEN title IS NULL THEN 20 ELSE 0 END)
                        - (CASE WHEN description IS NULL THEN 10 ELSE 0 END)
                        - (CASE WHEN noindex = 1 THEN 20 ELSE 0 END)
                    ) as calculated_score'))
                    ->whereRaw('(100
                        - (CASE WHEN title IS NULL THEN 20 ELSE 0 END)
                        - (CASE WHEN description IS NULL THEN 10 ELSE 0 END)
                        - (CASE WHEN noindex = 1 THEN 20 ELSE 0 END)
                    ) < 100')
                    ->orderBy('calculated_score', 'asc')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('seoable_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->colors(['primary']),

                TextColumn::make('seoable_title')
                    ->label('Page Title')
                    ->limit(30)
                    ->state(function (?SeoMeta $record) {
                        return $record?->seoable?->title ?? $record?->seoable?->name ?? 'Unknown Page';
                    }),

                TextColumn::make('issues')
                    ->label('Issues')
                    ->badge()
                    ->separator(',')
                    ->state(function (?SeoMeta $record) {
                        if (!$record) return [];
                        $issues = [];
                        if (!$record->title || !$record->description) {
                            $issues[] = 'Meta Missing';
                        }
                        if ($record->noindex) {
                            $issues[] = 'Noindex';
                        }
                        $score = $record->calculated_score;

                        if ($score < 60) {
                            $issues[] = 'Critical Score';
                        }
                        return $issues;
                    })
                    ->colors([
                        'danger' => 'Critical Score',
                        'warning' => 'Meta Missing',
                        'gray' => 'Noindex',
                    ]),

                TextColumn::make('calculated_score')
                    ->label('Score')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        (int)$state >= 80 => 'success',
                        (int)$state >= 60 => 'warning',
                        default => 'danger',
                    }),
            ])
            ->actions([
                Action::make('edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn (?SeoMeta $record): ?string => $record ? SeoMetaResource::getUrl('edit', ['record' => $record]) : null),

                Action::make('audit')
                    ->label('Audit')
                    ->icon('heroicon-m-clipboard-document-check')
                    ->modalContent(function (?SeoMeta $record) {
                        if (!$record || !$record->seoable) return null;
                        $audit = app(SeoAuditService::class)->audit($record->seoable);

                        return view('filament.components.seo-audit-result', ['audit' => $audit]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->paginated(false);
    }
}
