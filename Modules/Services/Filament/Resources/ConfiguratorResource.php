<?php

namespace Modules\Services\Filament\Resources;

use Modules\Services\Filament\Resources\ConfiguratorResource\Pages;
use Modules\Services\Filament\Resources\ConfiguratorResource\RelationManagers;
use Modules\Services\Filament\Resources\ConfiguratorResource\Schemas\ConfiguratorForm;
use Modules\Services\Filament\Resources\ConfiguratorResource\Tables\ConfiguratorTable;
use Modules\Services\Models\Configurator;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ConfiguratorResource extends Resource
{
    protected static ?string $model = Configurator::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static string | \UnitEnum | null $navigationGroup = 'Service Management';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components(ConfiguratorForm::schema());
    }

    public static function table(Table $table): Table
    {
        return ConfiguratorTable::table($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StepsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConfigurators::route('/'),
            'create' => Pages\CreateConfigurator::route('/create'),
            'edit' => Pages\EditConfigurator::route('/{record}/edit'),
        ];
    }
}
