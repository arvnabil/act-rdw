<?php

namespace Modules\Events\Filament\Resources\EventUserResource\Pages;

use Modules\Events\Filament\Resources\EventUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEventUser extends CreateRecord
{
    protected static string $resource = EventUserResource::class;
}
