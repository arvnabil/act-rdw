<?php

namespace Modules\Settings\Filament\Resources;

use Modules\Settings\Filament\Resources\SettingResource\Pages;
use Modules\Settings\Filament\Resources\SettingResource\Schemas\SettingForm;
use Modules\Settings\Filament\Resources\SettingResource\Tables\SettingTable;
use Modules\Settings\Models\Setting;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema(SettingForm::schema());
    }

    public static function table(Table $table): Table
    {
        return SettingTable::table($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
