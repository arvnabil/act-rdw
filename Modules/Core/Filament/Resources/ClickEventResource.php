<?php

namespace Modules\Core\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\ClickEventResource\Pages;
use Modules\Core\Models\AnalyticsClickEvent;
use Modules\Core\Filament\Resources\ClickEventResource\Schemas\ClickEventForm;
use Modules\Core\Filament\Resources\ClickEventResource\Tables\ClickEventTable;

class ClickEventResource extends Resource
{
    protected static ?string $model = AnalyticsClickEvent::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cursor-arrow-rays';

    protected static string | \UnitEnum | null $navigationGroup = 'Analytics';

    protected static ?string $navigationLabel = 'Click Events';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema(ClickEventForm::schema());
    }

    public static function table(Table $table): Table
    {
        return ClickEventTable::table($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClickEvents::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
