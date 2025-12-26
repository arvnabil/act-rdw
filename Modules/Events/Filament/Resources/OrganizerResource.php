<?php

namespace Modules\Events\Filament\Resources;

use Modules\Events\Filament\Resources\OrganizerResource\Pages;
use Modules\Events\Filament\Resources\OrganizerResource\Schemas\OrganizerForm;
use Modules\Events\Filament\Resources\OrganizerResource\Tables\OrganizerTable;
use Modules\Events\Models\Organizer;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class OrganizerResource extends Resource
{
    protected static ?string $model = Organizer::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static string | \UnitEnum | null $navigationGroup = 'Events Management';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components(OrganizerForm::schema());
    }

    public static function table(Table $table): Table
    {
        return OrganizerTable::table($table);
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
            'index' => Pages\ListOrganizers::route('/'),
            'create' => Pages\CreateOrganizer::route('/create'),
            'edit' => Pages\EditOrganizer::route('/{record}/edit'),
        ];
    }
}
