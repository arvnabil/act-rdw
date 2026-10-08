<?php

namespace Modules\Events\Filament\Resources;

use Modules\Events\Filament\Resources\EventCertificateResource\Pages;
use Modules\Events\Filament\Resources\EventCertificateResource\Schemas\EventCertificateForm;
use Modules\Events\Filament\Resources\EventCertificateResource\Tables\EventCertificateTable;
use Modules\Events\Models\EventCertificate;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class EventCertificateResource extends Resource
{
    protected static ?string $model = EventCertificate::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string | \UnitEnum | null $navigationGroup = 'Event Manage Data';

    protected static ?string $navigationLabel = 'Event Certificates';

    protected static ?string $modelLabel = 'Event Certificate';

    protected static ?string $pluralModelLabel = 'Event Certificates';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components(EventCertificateForm::schema());
    }

    public static function table(Table $table): Table
    {
        return EventCertificateTable::table($table);
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
            'index' => Pages\ListEventCertificates::route('/'),
            'create' => Pages\CreateEventCertificate::route('/create'),
            'edit' => Pages\EditEventCertificate::route('/{record}/edit'),
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
