<?php

namespace Modules\Events\Filament\Resources\EventResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Events\Filament\Resources\EventResource;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;
}
