<?php

namespace Modules\Settings\Filament\Resources;

use Modules\Settings\Filament\Resources\ApiKeyResource\Pages;
use Modules\Settings\Filament\Resources\ApiKeyResource\Schemas\ApiKeyForm;
use Modules\Settings\Filament\Resources\ApiKeyResource\Tables\ApiKeyTable;
use Modules\Settings\Models\ApiKey;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ApiKeyResource extends Resource
{
    protected static ?string $model = ApiKey::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-key';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(ApiKeyForm::schema());
    }

    public static function table(Table $table): Table
    {
        return ApiKeyTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApiKeys::route('/'),
            'create' => Pages\CreateApiKey::route('/create'),
            'edit' => Pages\EditApiKey::route('/{record}/edit'),
        ];
    }
}
