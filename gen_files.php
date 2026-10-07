<?php
// Generator script untuk membuat semua file

 = [];

// =============================================
// RoleResource.php
// =============================================
['app/Filament/Activioncms/Resources/RoleResource.php'] = <<<'PHP'
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
    protected static ?string  = Role::class;
    protected static \BackedEnum|string|null  = 'heroicon-o-shield-check';
    protected static string|\UnitEnum|null  = 'User Management';
    protected static ?int  = 2;
    protected static ?string  = 'Roles & Permissions';
    protected static ?string  = 'Role';
    protected static ?string  = 'Roles and Permissions';

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('administrator') ?? false;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->hasRole('administrator') ?? false;
    }

    public static function canEdit(): bool
    {
        return Auth::user()?->hasRole('administrator') && ->name !== 'administrator';
    }

    public static function canDelete(): bool
    {
         = ['administrator', 'co-admin', 'editor', 'viewer'];
        return Auth::user()?->hasRole('administrator') && !in_array(->name, );
    }

    public static function form(Schema ): Schema
    {
        return ->components([
            Section::make('Role Details')
                ->schema([
                    TextInput::make('name')
                        ->label('Role Name')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(100)
                        ->helperText('Use lowercase with hyphens, e.g. 
