<?php

namespace Modules\FormBuilder\Filament\Resources\FormSubmissionResource\Pages;

use Modules\FormBuilder\Filament\Resources\FormSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFormSubmissions extends ListRecords
{
    protected static string $resource = FormSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \Modules\FormBuilder\Filament\Widgets\FormSecurityOverview::class,
        ];
    }
}
