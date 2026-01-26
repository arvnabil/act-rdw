<?php

namespace App\Filament\Activioncms\Pages;

use App\Models\SeoMonitoringRecord;
use App\Services\SeoMonitoringService;
use App\Filament\Activioncms\Widgets\SeoIndexStatusChart;
use App\Filament\Activioncms\Widgets\SeoContentTypeChart;
use Filament\Pages\Page;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;

class IndexCoverage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar';
    protected static string | \UnitEnum | null $navigationGroup = 'SEO';
    protected static ?string $navigationLabel = 'Index Coverage';
    protected static ?string $title = 'SEO Index Coverage';
    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.activioncms.pages.index-coverage';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync')
                ->label('Sync Data')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    app(SeoMonitoringService::class)->sync();
                    Notification::make()
                        ->success()
                        ->title('SEO Data Synced')
                        ->send();
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SeoIndexStatusChart::class,
            SeoContentTypeChart::class,
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(SeoMonitoringRecord::query())
            ->columns([
                TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->wrap()
                    ->url(fn ($record) => $record->url, true),

                BadgeColumn::make('model')
                    ->label('Type')
                    ->colors([
                        'primary' => fn ($state) => in_array($state, ['Page', 'Static:Home']),
                        'success' => 'Product',
                        'warning' => 'Service',
                        'info' => 'News',
                    ]),

                BadgeColumn::make('is_noindex')
                    ->label('Index Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Noindex' : 'Indexed')
                    ->colors([
                        'danger' => 'Noindex',
                        'success' => 'Indexed',
                    ]),

                IconColumn::make('in_sitemap')
                    ->label('In Sitemap')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                IconColumn::make('canonical_valid')
                    ->label('Canonical')
                    ->boolean(),

                TextColumn::make('seo_score')
                    ->label('Score')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "$state%"),

                TextColumn::make('last_modified')
                    ->label('Last Modified')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('model')
                    ->label('Type')
                    ->options([
                        'Page' => 'Page',
                        'Product' => 'Product',
                        'Service' => 'Service',
                        'News' => 'News',
                        'Brand' => 'Brand',
                        'Project' => 'Project',
                    ]),

                SelectFilter::make('is_noindex')
                    ->label('Index Status')
                    ->options([
                        '0' => 'Indexed',
                        '1' => 'Noindex',
                    ]),

                SelectFilter::make('in_sitemap')
                    ->label('Sitemap Status')
                    ->options([
                        '1' => 'In Sitemap',
                        '0' => 'Not in Sitemap',
                    ]),
            ])
            ->actions([
                Action::make('edit')
                    ->label('Edit Record')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => $this->getEditUrl($record))
                    ->hidden(fn ($record) => str_starts_with($record->model, 'Static')),
            ]);
    }

    protected function getEditUrl($record): ?string
    {
        $map = [
            'Page' => 'filament.activioncms.resources.pages.edit',
            'Brand' => 'filament.activioncms.resources.brands.edit',
            'News' => 'filament.activioncms.resources.news.edit',
            'Project' => 'filament.activioncms.resources.projects.edit',
            'Product' => 'filament.activioncms.resources.products.edit',
            'Service' => 'filament.activioncms.resources.services.edit',
            'ServiceSolution' => 'filament.activioncms.resources.services.edit',
            'NewsCategory' => 'filament.activioncms.resources.news-categories.edit',
        ];

        $routeName = $map[$record->model] ?? null;
        if (!$routeName || !Route::has($routeName)) return null;

        return route($routeName, ['record' => $record->model_id]);
    }
}
