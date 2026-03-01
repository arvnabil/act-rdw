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
}
