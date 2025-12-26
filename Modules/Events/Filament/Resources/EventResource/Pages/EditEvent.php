<?php

namespace Modules\Events\Filament\Resources\EventResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\Events\Filament\Resources\EventResource;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;
}
