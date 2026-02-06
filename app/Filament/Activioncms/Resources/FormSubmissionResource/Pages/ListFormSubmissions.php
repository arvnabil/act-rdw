<?php

namespace App\Filament\Activioncms\Resources\FormSubmissionResource\Pages;

use App\Filament\Activioncms\Resources\FormSubmissionResource;
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
            \App\Filament\Activioncms\Widgets\FormSecurityOverview::class,
        ];
    }
}
