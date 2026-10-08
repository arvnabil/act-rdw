<?php

namespace Modules\FormBuilder\Filament\Resources;

use Modules\FormBuilder\Filament\Resources\FormSubmissionResource\Pages;
use Modules\FormBuilder\Filament\Resources\FormSubmissionResource\Schemas\FormSubmissionForm;
use Modules\FormBuilder\Filament\Resources\FormSubmissionResource\Tables\FormSubmissionTable;
use Modules\FormBuilder\Models\FormSubmission;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FormSubmissionResource extends Resource
{
    protected static ?string $model = FormSubmission::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static string | \UnitEnum | null $navigationGroup = 'Form Management';

    protected static ?string $navigationLabel = 'Form Inbox';

    protected static ?string $modelLabel = 'Form Inbox';

    protected static ?string $pluralModelLabel = 'Form Inbox';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(FormSubmissionForm::schema());
    }

    public static function table(Table $table): Table
    {
        return FormSubmissionTable::table($table);
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
            'index' => Pages\ListFormSubmissions::route('/'),
            'view' => Pages\ViewFormSubmission::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasAnyPermission(['view_any_form', 'view_form', 'create_form', 'update_form', 'delete_form']);
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('update_form');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('administrator')
            || (bool) auth()->user()?->hasPermissionTo('delete_form');
    }
}
