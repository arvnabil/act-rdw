<?php

namespace Modules\Core\Filament\Resources\WhatsAppAnalyticsResource\Tables;

use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Core\Models\AnalyticsClickEvent;
use Barryvdh\DomPDF\Facade\Pdf;

class WhatsAppAnalyticsTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Time'),
                Tables\Columns\TextColumn::make('event_label')
                    ->label('Label / Product Name')
                    ->searchable()
                    ->sortable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('entity_slug')
                    ->label('Entity ID/Slug')
                    ->searchable()
                    ->toggleable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('click_count')
                    ->label('Clicks')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_bot')
                    ->label('Source')
                    ->badge()
                    ->color(fn ($state) => $state ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => $state ? 'Bot' : 'Human')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_converted')
                    ->label('Converted')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('utm_source')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->default('-')
                    ->color('success'),
                Tables\Columns\TextColumn::make('utm_medium')
                    ->searchable()
                    ->default('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('utm_campaign')
                    ->searchable()
                    ->default('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('cta_position')
                    ->searchable()
                    ->default('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('device')
                    ->badge()
                    ->color(fn ($state) => $state === 'mobile' ? 'warning' : 'info')
                    ->default('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('city')
                    ->description(fn ($record) => $record->country)
                    ->default('-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('page_url')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->page_url)
                    ->icon('heroicon-m-globe-alt')
                    ->default('-')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('utm_source')
                    ->options(fn () => \Modules\Core\Models\AnalyticsClickEvent::query()->where('event_type', 'whatsapp')->distinct()->pluck('utm_source', 'utm_source')->filter()->toArray()),
                Tables\Filters\SelectFilter::make('utm_medium')
                    ->options(fn () => \Modules\Core\Models\AnalyticsClickEvent::query()->where('event_type', 'whatsapp')->distinct()->pluck('utm_medium', 'utm_medium')->filter()->toArray()),
                Tables\Filters\SelectFilter::make('utm_campaign')
                    ->options(fn () => \Modules\Core\Models\AnalyticsClickEvent::query()->where('event_type', 'whatsapp')->distinct()->pluck('utm_campaign', 'utm_campaign')->filter()->toArray()),
                Tables\Filters\SelectFilter::make('utm_content')
                    ->options(fn () => \Modules\Core\Models\AnalyticsClickEvent::query()->where('event_type', 'whatsapp')->distinct()->pluck('utm_content', 'utm_content')->filter()->toArray()),
                Tables\Filters\SelectFilter::make('cta_position')
                    ->options(fn () => \Modules\Core\Models\AnalyticsClickEvent::query()->where('event_type', 'whatsapp')->distinct()->pluck('cta_position', 'cta_position')->filter()->toArray()),
                Tables\Filters\SelectFilter::make('entity_type')
                    ->options([
                        'product' => 'Product',
                        'service' => 'Service (Category)',
                        'service_solution' => 'Service (Detail)',
                        'brand' => 'Brand',
                    ]),
                Tables\Filters\SelectFilter::make('device')
                    ->options([
                        'mobile' => 'Mobile',
                        'desktop' => 'Desktop',
                    ]),
                Tables\Filters\SelectFilter::make('city')
                    ->options(fn () => \Modules\Core\Models\AnalyticsClickEvent::query()->where('event_type', 'whatsapp')->whereNotNull('city')->distinct()->pluck('city', 'city')->toArray()),
                Tables\Filters\Filter::make('converted_only')
                    ->query(fn ($query) => $query->where('is_converted', true))
                    ->label('Show Converted Only'),
                Tables\Filters\Filter::make('human_only')
                    ->query(fn ($query) => $query->where('is_bot', false))
                    ->default(true)
                    ->label('Hide Bots'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from'),
                        \Filament\Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($query) => $query->whereDate('created_at', '>=', $data['created_from']))
                            ->when($data['created_until'], fn ($query) => $query->whereDate('created_at', '<=', $data['created_until']));
                    })
            ])
            ->headerActions([
                Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $dateRange = [
                            'from' => $records->min('created_at'),
                            'to' => $records->max('created_at'),
                        ];
                        
                        $pdf = Pdf::loadView('exports.whatsapp-report', [
                            'records' => $records,
                            'dateRange' => $dateRange,
                        ])->setPaper('a4', 'landscape');
                        
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'whatsapp-report-' . now()->format('Y-m-d-H-i') . '.pdf'
                        );
                    }),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
