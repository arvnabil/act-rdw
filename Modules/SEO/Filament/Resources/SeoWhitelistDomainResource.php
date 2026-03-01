<?php

namespace Modules\SEO\Filament\Resources;

use Modules\SEO\Filament\Resources\SeoWhitelistDomainResource\Pages;
use Modules\SEO\Filament\Resources\SeoWhitelistDomainResource\Schemas\SeoWhitelistDomainForm;
use Modules\SEO\Filament\Resources\SeoWhitelistDomainResource\Tables\SeoWhitelistDomainTable;
use Modules\SEO\Models\SeoWhitelistDomain;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SeoWhitelistDomainResource extends Resource
{
    protected static ?string $model = SeoWhitelistDomain::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string | \UnitEnum | null $navigationGroup = 'Seo Management';

    protected static ?string $navigationLabel = 'Whitelist Domains';

    protected static ?string $pluralLabel = 'Whitelist Domains';

    public static function form(Schema $schema): Schema
    {
        return $schema->components(SeoWhitelistDomainForm::schema());
    }

    public static function table(Table $table): Table
    {
        return SeoWhitelistDomainTable::table($table);
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
            'index' => Pages\ListSeoWhitelistDomains::route('/'),
            'create' => Pages\CreateSeoWhitelistDomain::route('/create'),
            'edit' => Pages\EditSeoWhitelistDomain::route('/{record}/edit'),
        ];
    }
}
