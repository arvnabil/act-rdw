<?php

namespace Modules\Events\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\Events\Models\EventRegistration;

class LatestRegistrationsTable extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                EventRegistration::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered At')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Event'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Registered' => 'success',
                        'Certified' => 'primary',
                        'Rejected', 'Cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->paginated(false); 
    }
}
