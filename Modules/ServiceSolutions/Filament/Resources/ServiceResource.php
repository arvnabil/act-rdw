<?php

namespace Modules\ServiceSolutions\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\ServiceSolutions\Filament\Resources\ServiceResource\Pages;
use Modules\ServiceSolutions\Filament\Resources\ServiceResource\RelationManagers\ServiceCategoryRelationManager;
use Modules\ServiceSolutions\Filament\Resources\ServiceResource\RelationManagers\ServiceSolutionRelationManager;
use Modules\ServiceSolutions\Filament\Resources\ServiceResource\Schemas\ServiceForm;
use Modules\ServiceSolutions\Filament\Resources\ServiceResource\Tables\ServiceTable;
use Modules\ServiceSolutions\Models\Service;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static string | \UnitEnum | null $navigationGroup = 'Service Management';

    protected static ?string $navigationLabel = 'Services';

    protected static ?string $modelLabel = 'Service';

    protected static ?string $pluralModelLabel = 'Services';

    protected static ?int $navigationSort = 1;
    public static function form(Schema $schema): Schema
    {
        return $schema->components(ServiceForm::schema());
    }

    public static function table(Table $table): Table
    {
        return ServiceTable::table($table);
    }

    public static function getRelations(): array
    {
        return [
            ServiceCategoryRelationManager::class,
            ServiceSolutionRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
