<?php

namespace Modules\AI\Filament\Resources;

use Modules\AI\Models\ChatSession;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Repeater;

class ChatSessionResource extends Resource
{
    protected static ?string $model = ChatSession::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string | \UnitEnum | null $navigationGroup = 'AI Management';

    protected static ?string $label = 'AI Chat Session';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Lead Information')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextInput::make('name')->readOnly(),
                            TextInput::make('whatsapp')->label('WhatsApp')->readOnly(),
                            TextInput::make('email')->readOnly(),
                            TextInput::make('company')->readOnly(),
                            TextInput::make('created_at')->readOnly(),
                            TextInput::make('status')->readOnly(),
                        ])
                ]),
            Section::make('AI Summary')
                ->schema([
                    Textarea::make('summary')->label('')->rows(3)->readOnly(),
                ]),
            Section::make('Conversation Log')
                ->schema([
                    Repeater::make('messages')
                        ->relationship('messages')
                        ->schema([
                            TextInput::make('role')->readOnly(),
                            Textarea::make('content')->readOnly()->rows(2),
                        ])
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('WA')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'gray',
                        'active' => 'warning',
                        'closed' => 'success',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                EditAction::make()->label('View Details'),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\AI\Filament\Resources\ChatSessionResource\Pages\ListChatSessions::route('/'),
            'edit' => \Modules\AI\Filament\Resources\ChatSessionResource\Pages\EditChatSession::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
