<?php

namespace Modules\Campaign\Filament\Resources;

use Modules\Campaign\Filament\Resources\CampaignResource\Pages;
use Modules\Campaign\Filament\Resources\CampaignResource\Schemas\CampaignForm;
use Modules\Campaign\Filament\Resources\CampaignResource\Tables\CampaignTable;
use Modules\Campaign\Models\Campaign;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-megaphone';

    protected static string|\UnitEnum|null $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Campaigns';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components(CampaignForm::schema());
    }

    public static function table(Table $table): Table
    {
        return CampaignTable::table($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit'   => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }
}