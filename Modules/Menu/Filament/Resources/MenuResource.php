<?php

namespace Modules\Menu\Filament\Resources;

use Modules\Menu\Filament\Resources\MenuResource\Pages;
use Modules\Menu\Filament\Resources\MenuResource\Schemas\MenuForm;
use Modules\Menu\Filament\Resources\MenuResource\Tables\MenuTable;
use Modules\Menu\Models\Menu;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-bars-3';

    protected static string | \UnitEnum | null $navigationGroup = 'Menu Management';

    protected static ?string $navigationLabel = 'Menu';

    protected static ?string $modelLabel = 'Menu';

    protected static ?string $pluralModelLabel = 'Menus';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(MenuForm::schema());
    }

    public static function table(Table $table): Table
    {
        return MenuTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
