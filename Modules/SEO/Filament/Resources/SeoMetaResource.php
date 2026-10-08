<?php

namespace Modules\SEO\Filament\Resources;

use Modules\SEO\Filament\Resources\SeoMetaResource\Schemas\SeoForm;
use Modules\SEO\Filament\Resources\SeoMetaResource\Pages;
use Modules\SEO\Filament\Resources\SeoMetaResource\Tables\SeoMetaTable;
use Modules\SEO\Models\SeoMeta;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SeoMetaResource extends Resource
{
    protected static ?string $model = SeoMeta::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-magnifying-glass-circle';

    protected static string | \UnitEnum | null $navigationGroup = 'Seo Management';

    protected static ?string $navigationLabel = 'SEO Audit';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(SeoForm::schema());
    }

    public static function table(Table $table): Table
    {
        return SeoMetaTable::table($table);
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
            'index' => Pages\ListSeoMetas::route('/'),
            'edit' => Pages\EditSeoMeta::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasAnyPermission(['view_seo', 'update_seo']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('update_seo');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('update_seo');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('update_seo');
    }
}
