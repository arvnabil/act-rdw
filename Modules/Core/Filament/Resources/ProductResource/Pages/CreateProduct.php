<?php

namespace Modules\Core\Filament\Resources\ProductResource\Pages;

use Modules\Core\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
}
