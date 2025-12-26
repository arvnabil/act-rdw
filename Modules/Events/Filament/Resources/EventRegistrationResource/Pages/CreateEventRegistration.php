<?php

namespace Modules\Events\Filament\Resources\EventRegistrationResource\Pages;

use Modules\Events\Filament\Resources\EventRegistrationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEventRegistration extends CreateRecord
{
    protected static string $resource = EventRegistrationResource::class;
}
