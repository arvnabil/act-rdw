<?php

namespace Modules\SEO\Filament\Resources\SeoWhitelistDomainResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;

class SeoWhitelistDomainForm
{
    public static function schema(): array
    {
        return [
            Section::make('Domain Information')
                ->schema([
                    TextInput::make('domain')
                        ->label('Domain Name')
                        ->placeholder('example.com')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Input domain without http:// or https:// (e.g., wikipedia.org)'),
                    
                    Textarea::make('description')
                        ->rows(3)
                        ->placeholder('Brief description about this domain...'),
                    
                    Toggle::make('is_active')
                        ->label('Is Active')
                        ->default(true)
                        ->helperText('Only active domains will be treated as Dofollow.'),
                ])
        ];
    }
}
