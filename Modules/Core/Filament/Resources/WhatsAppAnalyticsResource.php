<?php

namespace Modules\Core\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Modules\Core\Filament\Resources\WhatsAppAnalyticsResource\Pages;
use Modules\Core\Models\AnalyticsWhatsapp;
use Modules\Core\Filament\Resources\WhatsAppAnalyticsResource\Schemas\WhatsAppAnalyticsForm;
use Modules\Core\Filament\Resources\WhatsAppAnalyticsResource\Tables\WhatsAppAnalyticsTable;

class WhatsAppAnalyticsResource extends Resource
{
    protected static ?string $model = \Modules\Core\Models\AnalyticsClickEvent::class;

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('event_type', 'whatsapp');
    }

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string | \UnitEnum | null $navigationGroup = 'Analytics';
    
    protected static ?string $navigationLabel = 'WhatsApp Logs';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema(WhatsAppAnalyticsForm::schema());
    }

    public static function table(Table $table): Table
    {
        return WhatsAppAnalyticsTable::table($table);
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
            'index' => Pages\ListWhatsAppAnalytics::route('/'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}
