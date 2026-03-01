<?php

namespace Modules\Projects\Filament\Resources;

use Modules\Projects\Filament\Resources\ProjectResource\Pages;
use Modules\Projects\Filament\Resources\ProjectResource\Schemas\ProjectForm;
use Modules\Projects\Filament\Resources\ProjectResource\Tables\ProjectTable;
use Modules\Projects\Models\Project;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-briefcase';

    protected static string | \UnitEnum | null $navigationGroup = 'Project Management';

    public static function form(Schema $schema): Schema
    {
        return $schema->components(ProjectForm::schema());
    }

    public static function table(Table $table): Table
    {
        return ProjectTable::table($table);
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
