<?php

namespace App\Filament\Activioncms\Resources\SeoMetaResource\Tables;

use App\Models\SeoMeta;
use App\Services\Seo\SeoAuditService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class SeoMetaTable
{
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('seoable_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->badge(),
                TextColumn::make('seoable.title')
                    ->label('Page Title')
                    ->state(fn (?SeoMeta $record) => $record?->seoable?->title ?? $record?->seoable?->name ?? 'Untitled')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Meta Title')
                    ->limit(30)
                    ->description(fn (?SeoMeta $record) => $record?->title ? 'Set' : 'Missing layer'),

                IconColumn::make('status_check')
                    ->label('Status')
                    ->state(fn (?SeoMeta $record) => $record?->title && $record?->description)
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('success')
                    ->falseColor('warning'),

                IconColumn::make('noindex')
                    ->boolean()
                    ->label('No Index'),

                TextColumn::make('seo_score')
                    ->label('Score')
                    ->state(function (?SeoMeta $record) {
                        if (!$record?->seoable) {
                            return 'N/A';
                        }
                        // Calculate score in real-time for consistency with modal
                        return app(SeoAuditService::class)->calculateScore($record->seoable);
                    })
                    ->badge()
                    ->color(fn ($state): string => match (true) {
                        $state === 'N/A' => 'gray',
                        $state >= 80 => 'success',
                        $state >= 50 => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('path')
                    ->label('Route / Path')
                    ->state(fn (?SeoMeta $record) => match (true) {
                        !$record || !$record->seoable => 'Missing Page (Orphaned)',
                        $record->seoable instanceof \App\Models\Page && $record->seoable->is_homepage => '/',
                        $record->seoable instanceof \App\Models\News => '/news/' . $record->seoable->slug,
                        $record->seoable instanceof \Modules\ServiceSolutions\Models\Service => '/services/' . $record->seoable->slug,
                        $record->seoable instanceof \App\Models\Project => '/projects/' . $record->seoable->slug,
                        $record->seoable instanceof \Modules\Core\Models\Product => '/products/' . $record->seoable->slug,
                        default => '/' . ($record->seoable->slug ?? '')
                    })
                    ->color('gray')
                    ->fontFamily('mono'),
            ])
            ->filters([
                Filter::make('missing_metadata')
                    ->label('Needs Attention')
                    ->query(fn ($query) => $query->whereNull('title')->orWhereNull('description')),

                Filter::make('noindex')
                    ->label('No Index Pages')
                    ->query(fn ($query) => $query->where('noindex', true)),
            ])
            ->actions([
                EditAction::make(),
                Action::make('audit')
                    ->label('Audit')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->modalContent(function (?SeoMeta $record) {
                        if (!$record || !$record->seoable) return null;
                        $audit = app(SeoAuditService::class)->audit($record->seoable);

                        return view('filament.components.seo-audit-result', ['audit' => $audit]);
                    })
                    ->visible(fn (?SeoMeta $record) => (bool) $record?->seoable)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->bulkActions([]);
    }
}
