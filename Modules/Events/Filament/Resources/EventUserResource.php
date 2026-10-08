<?php

namespace Modules\Events\Filament\Resources;

use Modules\Events\Filament\Resources\EventUserResource\Pages;
use Modules\Events\Filament\Resources\EventUserResource\Schemas\EventUserForm;
use Modules\Events\Filament\Resources\EventUserResource\Tables\EventUserTable;
use Modules\Events\Models\EventUser;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class EventUserResource extends Resource
{
    protected static ?string $model = EventUser::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Event Manage Data';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components(EventUserForm::schema());
    }

    public static function table(Table $table): Table
    {
        return EventUserTable::table($table);
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
            'index' => Pages\ListEventUsers::route('/'),
            'create' => Pages\CreateEventUser::route('/create'),
            'edit' => Pages\EditEventUser::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasAnyPermission(['view_any_event', 'view_event', 'create_event', 'update_event', 'delete_event']);
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
