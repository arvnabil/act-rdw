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

    protected static string | \UnitEnum | null $navigationGroup = 'Event Management';

    protected static ?string $navigationLabel = 'Events';

    protected static ?string $modelLabel = 'Event';

    protected static ?string $pluralModelLabel = 'Events';

    protected static ?int $navigationSort = 3;

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

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasAnyPermission(['view_any_event', 'view_event', 'create_event', 'update_event', 'delete_event', 'delete_any_event']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('create_event');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('update_event');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('delete_event');
    }
}
