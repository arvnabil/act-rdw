<?php

namespace Modules\Services\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Modules\Services\Filament\Resources\ServiceResource\Pages;
use Modules\Services\Filament\Resources\ServiceResource\RelationManagers\ServiceCategoryRelationManager;
use Modules\Services\Filament\Resources\ServiceResource\RelationManagers\ServiceSolutionRelationManager;
use Modules\Services\Filament\Resources\ServiceResource\Schemas\ServiceForm;
use Modules\Services\Filament\Resources\ServiceResource\Tables\ServiceTable;
use Modules\Services\Models\Service;

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

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasAnyPermission(['view_any_service', 'view_service', 'create_service', 'update_service', 'delete_service', 'delete_any_service']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('create_service');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('update_service');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('delete_service');
    }
}
