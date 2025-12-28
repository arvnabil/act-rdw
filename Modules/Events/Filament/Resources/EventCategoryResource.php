<?php

namespace Modules\Events\Filament\Resources;

use Modules\Events\Filament\Resources\EventCategoryResource\Pages;
use Modules\Events\Models\EventCategory;
use Modules\Events\Filament\Resources\EventCategoryResource\Schemas\EventCategoryForm;
use Modules\Events\Filament\Resources\EventCategoryResource\Tables\EventCategoryTable;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Str;

class EventCategoryResource extends Resource
{
    protected static ?string $model = EventCategory::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    protected static string | \UnitEnum | null $navigationGroup = 'Event Management';

    protected static ?string $navigationLabel = 'Event Categories';

    protected static ?string $modelLabel = 'Event Category';

    protected static ?string $pluralModelLabel = 'Event Categories';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components(EventCategoryForm::schema());
    }

    public static function table(Table $table): Table
    {
        return EventCategoryTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventCategories::route('/'),
            'create' => Pages\CreateEventCategory::route('/create'),
            'edit' => Pages\EditEventCategory::route('/{record}/edit'),
        ];
    }
}
