<?php

namespace Modules\Events\Filament\Resources;

use Modules\Events\Filament\Resources\EventDocumentationResource\Pages;
use Modules\Events\Filament\Resources\EventDocumentationResource\Schemas\EventDocumentationForm;
use Modules\Events\Filament\Resources\EventDocumentationResource\Tables\EventDocumentationTable;
use Modules\Events\Models\EventDocumentation;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class EventDocumentationResource extends Resource
{
    protected static ?string $model = EventDocumentation::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-camera';

    protected static string | \UnitEnum | null $navigationGroup = 'Event Management';

    protected static ?string $navigationLabel = 'Event Documentations';

    protected static ?string $modelLabel = 'Event Documentation';

    protected static ?string $pluralModelLabel = 'Event Documentations';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components(EventDocumentationForm::schema());
    }

    public static function table(Table $table): Table
    {
        return EventDocumentationTable::table($table);
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
            'index' => Pages\ListEventDocumentations::route('/'),
            'create' => Pages\CreateEventDocumentation::route('/create'),
            'edit' => Pages\EditEventDocumentation::route('/{record}/edit'),
        ];
    }
}
