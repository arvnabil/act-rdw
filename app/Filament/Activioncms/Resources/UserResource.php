<?php

namespace App\Filament\Activioncms\Resources;

use App\Filament\Activioncms\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users';

    /**
     * Hanya Administrator yang bisa mengakses User Management
     */
    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('administrator')
            || (bool) Auth::user()?->hasAnyPermission([
                'view_any_user', 'view_user', 'create_user', 'update_user', 'delete_user', 'delete_any_user',
            ]);
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->hasRole('administrator') ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->hasRole('administrator') ?? false;
    }

    public static function canDelete($record): bool
    {
        return (Auth::user()?->hasRole('administrator') ?? false) && $record->id !== Auth::id();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('User Information')
                ->description('Basic user account details')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                    ]),

                    Grid::make(2)->schema([
                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->minLength(8)
                            ->helperText('Leave blank to keep current password when editing'),
                    ]),
                ]),

            Section::make('Role & Access')
                ->description('Assign role and manage access permissions')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('roles')
                            ->label('Role')
                            ->relationship('roles', 'name')
                            ->options(
                                Role::all()->pluck('name', 'id')
                                    ->map(fn ($name) => ucwords(str_replace('-', ' ', $name)))
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Roles determine what data the user can access and manage'),

                        Toggle::make('is_active')
                            ->label('Active Account')
                            ->helperText('Inactive users cannot access the admin panel')
                            ->default(true),
                    ]),
                ]),

            Section::make('Avatar')
                ->description('Profile picture')
                ->collapsed()
                ->schema([
                    FileUpload::make('avatar')
                        ->label('Profile Avatar')
                        ->image()
                        ->disk('public')
                        ->directory('avatars')
                        ->imageEditor()
                        ->circleCropper()
                        ->maxSize(2048),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=7F9CF5&background=EBF4FF')
                    ->size(40),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('-', ' ', $state)))
                    ->color(fn ($state) => match ($state) {
                        'administrator' => 'danger',
                        'co-admin'      => 'warning',
                        'editor'        => 'success',
                        'viewer'        => 'gray',
                        default         => 'primary',
                    }),

                ToggleColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Filter by Role')
                    ->relationship('roles', 'name')
                    ->options(
                        Role::all()->pluck('name', 'id')
                            ->map(fn ($name) => ucwords(str_replace('-', ' ', $name)))
                    ),

                TernaryFilter::make('is_active')
                    ->label('Account Status')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->native(false),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn ($record) => $record->id === Auth::id()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
