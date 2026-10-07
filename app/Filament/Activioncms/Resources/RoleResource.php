<?php

namespace App\Filament\Activioncms\Resources;

use App\Filament\Activioncms\Resources\RoleResource\Pages;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-shield-check';
    protected static string|\UnitEnum|null $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Roles & Permissions';
    protected static ?string $modelLabel = 'Role';
    protected static ?string $pluralModelLabel = 'Roles & Permissions';

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('administrator') ?? false;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->hasRole('administrator') ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->hasRole('administrator') && $record->name !== 'administrator';
    }

    public static function canDelete($record): bool
    {
        $protected = ['administrator', 'co-admin', 'editor', 'viewer'];
        return Auth::user()?->hasRole('administrator') && !in_array($record->name, $protected);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Role Details')
                ->schema([
                    TextInput::make('name')
                        ->label('Role Name')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(100)
                        ->helperText('Use lowercase with hyphens: e.g. content-writer')
                        ->disabled(fn ($record) => $record && in_array($record->name, ['administrator', 'co-admin', 'editor', 'viewer'])),
                ]),
            Section::make('Permissions')
                ->description('Select what this role is allowed to do')
                ->schema([
                    CheckboxList::make('permissions')
                        ->label('Permissions')
                        ->relationship('permissions', 'name')
                        ->options(fn () => Permission::all()->pluck('name', 'id'))
                        ->columns(3)
                        ->searchable()
                        ->bulkToggleable(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Role Name')
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('-', ' ', $state)))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'administrator' => 'danger',
                        'co-admin'      => 'warning',
                        'editor'        => 'success',
                        'viewer'        => 'gray',
                        default         => 'info',
                    }),
                TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions')
                    ->badge()
                    ->color('gray')
                    ->suffix(' permissions'),
                TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->badge()
                    ->color('primary')
                    ->suffix(' users'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
            ->actions([
                EditAction::make()
                    ->hidden(fn ($record) => $record->name === 'administrator'),
                DeleteAction::make()
                    ->hidden(fn ($record) => in_array($record->name, ['administrator', 'co-admin', 'editor', 'viewer'])),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit'   => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
