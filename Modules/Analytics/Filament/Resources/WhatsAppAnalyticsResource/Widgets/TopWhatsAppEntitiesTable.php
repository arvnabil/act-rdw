<?php

namespace Modules\Analytics\Filament\Resources\WhatsAppAnalyticsResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\Analytics\Models\AnalyticsClickEvent;
use Illuminate\Support\Facades\DB;

class TopWhatsAppEntitiesTable extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Top 10 WhatsApp Entities';

    public function table(Table $table): Table
    {
        // Get distinct cta_position values for the filter
        $positions = DB::table('analytics_click_events')
            ->where('event_type', 'whatsapp')
            ->where('is_bot', false)
            ->whereNull('deleted_at')
            ->whereNotNull('cta_position')
            ->distinct()
            ->pluck('cta_position')
            ->mapWithKeys(fn ($v) => [$v => $v])
            ->toArray();

        return $table
            ->query(function () {
                // Default query — will be modified by filters
                return $this->buildQuery();
            })
            ->defaultSort('total_clicks', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('entity_slug')
                    ->label('Slug')
                    ->description(fn ($record) => $record->entity_type ? strtoupper($record->entity_type) : 'GLOBAL')
                    ->placeholder('(Umum/Langsung)'),

                Tables\Columns\TextColumn::make('cta_position')
                    ->label('CTA Position')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('unique_clicks')
                    ->label('Unique Clicks')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('total_clicks')
                    ->label('Total Clicks')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('cta_position')
                    ->label('CTA Position')
                    ->options($positions)
                    ->query(function ($query, array $data) {
                        if (filled($data['value'])) {
                            // Rebuild the query with the cta_position filter
                            $query->fromSub($this->buildAggregatedQuery($data['value']), 'analytics_click_events');
                        }
                    }),
            ])
            ->paginated(false);
    }

    protected function buildQuery(?string $ctaPosition = null)
    {
        return AnalyticsClickEvent::query()
            ->withoutGlobalScopes()
            ->fromSub($this->buildAggregatedQuery($ctaPosition), 'analytics_click_events');
    }

    protected function buildAggregatedQuery(?string $ctaPosition = null)
    {
        $query = DB::table('analytics_click_events')
            ->where('event_type', 'whatsapp')
            ->where('is_bot', false)
            ->whereNull('deleted_at')
            ->select('entity_slug', 'entity_type', 'cta_position')
            ->selectRaw('COUNT(DISTINCT session_id) as unique_clicks')
            ->selectRaw('SUM(click_count) as total_clicks')
            ->selectRaw('MAX(id) as id')
            ->groupBy('entity_slug', 'entity_type', 'cta_position')
            ->orderByDesc('total_clicks')
            ->limit(10);

        if ($ctaPosition) {
            $query->where('cta_position', $ctaPosition);
        }

        return $query;
    }
}
