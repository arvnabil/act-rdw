<?php

namespace Modules\Analytics\Filament\Resources\ClickEventResource\Tables;

use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Modules\Analytics\Models\AnalyticsClickEvent;
use Barryvdh\DomPDF\Facade\Pdf;

class ClickEventTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['entity'])) // Eager load entity
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Time'),
                TextColumn::make('event_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'whatsapp' => 'success',
                        'call' => 'info',
                        'share' => 'warning',
                        'form_submit' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('event_label')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('click_count')
                    ->label('Clicks')
                    ->numeric()
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('is_bot')
                    ->label('Source')
                    ->badge()
                    ->color(fn ($state) => $state ? 'danger' : 'success')
                    ->formatStateUsing(fn ($state) => $state ? 'Bot' : 'Human')
                    ->toggleable(),
                IconColumn::make('is_converted')
                    ->label('Converted')
                    ->boolean()
                    ->toggleable(),
                TextColumn::make('deal_value')
                    ->money('IDR')
                    ->toggleable(),
                TextColumn::make('utm_source')
                    ->searchable()
                    ->badge()
                    ->default('-')
                    ->color('success'),
                TextColumn::make('utm_medium')
                    ->searchable()
                    ->default('-')
                    ->toggleable(),
                TextColumn::make('utm_campaign')
                    ->searchable()
                    ->default('-')
                    ->toggleable(),
                TextColumn::make('cta_position')
                    ->default('-')
                    ->searchable(),
                TextColumn::make('device')
                    ->badge()
                    ->color(fn ($state) => $state === 'mobile' ? 'warning' : 'info')
                    ->default('-')
                    ->toggleable(),
                TextColumn::make('city')
                    ->description(fn ($record) => $record->country)
                    ->default('-')
                    ->toggleable(),
                TextColumn::make('entity_slug')
                    ->label('Entity')
                    ->searchable()
                    ->default('-')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event_type')
                    ->options([
                        'whatsapp' => 'WhatsApp',
                        'call' => 'Call',
                        'share' => 'Share',
                        'form_submit' => 'Form Submit',
                        'download' => 'Download',
                    ]),
                Tables\Filters\SelectFilter::make('utm_source')
                    ->options(fn () => AnalyticsClickEvent::query()->distinct()->pluck('utm_source', 'utm_source')->filter()->toArray()),
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
             ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
                Actions\RestoreAction::make(),
                Actions\ForceDeleteAction::make(),
            ])
            ->headerActions([
                Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($livewire) {
                        // Increase memory limit for large datasets
                        ini_set('memory_limit', '512M');
                        set_time_limit(300);

                        $records = $livewire->getFilteredTableQuery()->get();
                        
                        // Limit records if it's still too large for the environment
                        if ($records->count() > 1500) {
                            $records = $records->take(1500);
                        }

                        $dateRange = [
                            'from' => $records->min('created_at'),
                            'to' => $records->max('created_at'),
                        ];
                        
                        $pdf = Pdf::loadView('exports.click-event-report', [
                            'records' => $records,
                            'dateRange' => $dateRange,
                        ])->setPaper('a4', 'landscape');
                        
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'click-events-report-' . now()->format('Y-m-d-H-i') . '.pdf'
                        );
                    }),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\RestoreBulkAction::make(),
                    Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
