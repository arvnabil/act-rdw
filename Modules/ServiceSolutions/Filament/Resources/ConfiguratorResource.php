<?php

namespace Modules\ServiceSolutions\Filament\Resources;

use Modules\ServiceSolutions\Filament\Resources\ConfiguratorResource\Pages;
use Modules\ServiceSolutions\Filament\Resources\ConfiguratorResource\RelationManagers;
use Modules\ServiceSolutions\Filament\Resources\ConfiguratorResource\Schemas\ConfiguratorForm;
use Modules\ServiceSolutions\Filament\Resources\ConfiguratorResource\Tables\ConfiguratorTable;
use Modules\ServiceSolutions\Models\Configurator;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ConfiguratorResource extends Resource
{
    protected static ?string $model = Configurator::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static string | \UnitEnum | null $navigationGroup = 'Master Data';

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
