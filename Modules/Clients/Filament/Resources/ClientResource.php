<?php

namespace Modules\Clients\Filament\Resources;

use Modules\Clients\Filament\Resources\ClientResource\Pages;
use Modules\Clients\Filament\Resources\ClientResource\Schemas\ClientForm;
use Modules\Clients\Filament\Resources\ClientResource\Tables\ClientTable;
use Modules\Clients\Models\Client;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Client Management';

    public static function form(Schema $schema): Schema
    {
        return $schema->components(ClientForm::schema());
    }

    public static function table(Table $table): Table
    {
        return ClientTable::table($table);
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasAnyPermission(['view_any_client', 'view_client', 'create_client', 'update_client', 'delete_client', 'delete_any_client']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('create_client');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('update_client');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('delete_client');
    }
}
