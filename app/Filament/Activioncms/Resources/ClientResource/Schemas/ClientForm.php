<?php

namespace App\Filament\Activioncms\Resources\ClientResource\Schemas;

use App\Models\Client;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use App\Filament\Activioncms\Resources\SeoMetaResource\Schemas\SeoForm;
use Filament\Schemas\Components\Group;
use Illuminate\Support\Str;

class ClientForm
{
    public static function schema(): array
    {
        return [
            Tabs::make('Tabs')
                ->tabs([
                    Tabs\Tab::make('General')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->live(onBlur: true),



                            FileUpload::make('logo')
                                ->image()
                                ->disk('public')
                                ->directory(fn (\Filament\Schemas\Components\Utilities\Get $get) => 'clients/' . (Str::slug($get('name')) ?? 'temp'))
                                ->visibility('public')
                                ->required(),

                            TextInput::make('website_url')
                                ->url(),

                            TextInput::make('position')
                                ->numeric()
                                ->default(0),

                            Toggle::make('is_active')
                                ->default(true),
                        ]),
                    Tabs\Tab::make('SEO')
                        ->schema([
                            Group::make()
                                ->relationship('seo')
                                ->schema(SeoForm::schema())
                        ]),
                ])->columnSpanFull()
        ];
    }
}
