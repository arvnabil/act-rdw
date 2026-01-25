<?php

namespace App\Filament\Activioncms\Resources;

use App\Filament\Activioncms\Resources\SettingResource\Pages;
use App\Filament\Activioncms\Resources\SettingResource\Schemas\SettingForm;
use App\Filament\Activioncms\Resources\SettingResource\Tables\SettingTable;
use App\Models\Setting;
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
