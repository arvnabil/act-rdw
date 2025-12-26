<?php

namespace Modules\Events\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Events\Filament\Resources\EventResource\Pages;
use Modules\Events\Filament\Resources\EventResource\Schemas\EventForm;
use Modules\Events\Filament\Resources\EventResource\Tables\EventTable;
use Modules\Events\Models\Event;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar';

    protected static string | \UnitEnum | null $navigationGroup = 'Events Management';

    protected static ?string $navigationLabel = 'Events';

    protected static ?string $modelLabel = 'Event';

    protected static ?string $pluralModelLabel = 'Events';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components(EventForm::schema());
    }

    public static function table(Table $table): Table
    {
        return EventTable::table($table);
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
