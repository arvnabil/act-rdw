<?php

namespace Modules\Events\Filament\Widgets;

use Filament\Widgets\Widget;

class EventModuleFeaturesWidget extends Widget
{
    protected string $view = 'events::filament.widgets.event-module-features-widget';
    
    protected static ?int $sort = 1;
    
    // Ensure column span is full for visibility
    protected int | string | array $columnSpan = 'full';
}
