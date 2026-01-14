<?php

namespace Modules\ServiceSolutions\Filament\Resources\ConfiguratorResource\RelationManagers;

use Modules\ServiceSolutions\Filament\Resources\ConfiguratorResource\Schemas\StepsForm;
use Modules\ServiceSolutions\Filament\Resources\ConfiguratorResource\Tables\StepsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class StepsRelationManager extends RelationManager
{
    protected static string $relationship = 'steps';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema->components(StepsForm::schema());
    }

    public function table(Table $table): Table
    {
        return StepsTable::table($table);
    }
}
