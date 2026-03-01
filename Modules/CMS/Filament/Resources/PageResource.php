<?php

namespace Modules\CMS\Filament\Resources;

use Modules\CMS\Filament\Resources\PageResource\Pages;
use Modules\CMS\Filament\Resources\PageResource\Schemas\PageForm;
use Modules\CMS\Filament\Resources\PageResource\Tables\PageTable;
use Modules\CMS\Models\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';

    protected static string | \UnitEnum | null $navigationGroup = 'Site Management';

    protected static ?string $navigationLabel = 'Page';

    protected static ?string $modelLabel = 'Page';

    protected static ?string $pluralModelLabel = 'Pages';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(PageForm::schema());
    }

    public static function table(Table $table): Table
    {
        return PageTable::table($table);
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
