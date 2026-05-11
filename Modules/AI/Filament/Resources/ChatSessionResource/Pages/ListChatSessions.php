<?php

namespace Modules\AI\Filament\Resources\ChatSessionResource\Pages;

use Modules\AI\Filament\Resources\ChatSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChatSessions extends ListRecords
{
    protected static string $resource = ChatSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action needed as sessions are created by the chatbot
        ];
    }
}
