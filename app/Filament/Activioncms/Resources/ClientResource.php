<?php

namespace App\Filament\Activioncms\Resources;

use App\Filament\Activioncms\Resources\ClientResource\Pages;
use App\Filament\Activioncms\Resources\ClientResource\Schemas\ClientForm;
use App\Filament\Activioncms\Resources\ClientResource\Tables\ClientTable;
use App\Models\Client;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Core';

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
