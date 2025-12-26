<?php

namespace Modules\Events\Filament\Resources;

use Modules\Events\Filament\Resources\EventRegistrationResource\Pages;
use Modules\Events\Filament\Resources\EventRegistrationResource\Schemas\EventRegistrationForm;
use Modules\Events\Filament\Resources\EventRegistrationResource\Tables\EventRegistrationTable;
use Modules\Events\Models\EventRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class EventRegistrationResource extends Resource
{
    protected static ?string $model = EventRegistration::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-ticket';

    protected static string | \UnitEnum | null $navigationGroup = 'Events Management';

    protected static ?string $navigationLabel = 'Event Registrations';

    protected static ?string $modelLabel = 'Event Registration';

    protected static ?string $pluralModelLabel = 'Event Registrations';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components(EventRegistrationForm::schema());
    }

    public static function table(Table $table): Table
    {
        return EventRegistrationTable::table($table);
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
            'index' => Pages\ListEventRegistrations::route('/'),
            'create' => Pages\CreateEventRegistration::route('/create'),
            'edit' => Pages\EditEventRegistration::route('/{record}/edit'),
        ];
    }
}
