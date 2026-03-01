<?php

namespace Modules\News\Filament\Resources\NewsCategoryResource\Pages;

use Modules\News\Filament\Resources\NewsCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNewsCategory extends CreateRecord
{
    protected static string $resource = NewsCategoryResource::class;
}
