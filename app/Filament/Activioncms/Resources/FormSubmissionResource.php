<?php

namespace App\Filament\Activioncms\Resources;

use App\Filament\Activioncms\Resources\FormSubmissionResource\Pages;
use App\Filament\Activioncms\Resources\FormSubmissionResource\Schemas\FormSubmissionForm;
use App\Filament\Activioncms\Resources\FormSubmissionResource\Tables\FormSubmissionTable;
use App\Models\FormSubmission;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class FormSubmissionResource extends Resource
{
    protected static ?string $model = FormSubmission::class;


    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-inbox-arrow-down';


    protected static string | \UnitEnum | null $navigationGroup = 'Site Management';

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
}
