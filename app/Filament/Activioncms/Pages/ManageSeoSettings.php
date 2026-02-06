<?php

namespace App\Filament\Activioncms\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use App\Models\Setting;

class ManageSeoSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-globe-alt';
    protected static string | \UnitEnum | null $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Global SEO';
    protected static ?string $title = 'Global SEO Settings';
    protected string $view = 'filament.activioncms.pages.manage-seo-settings';

    public ?array $data = [];

    public function mount(): void
    {
        // Load existing settings
        $settings = Setting::whereIn('key', [
            'seo_ga4_id',
            'seo_gtm_id',
            'seo_gsc_verification',
            'seo_default_title',
            'seo_default_description',
            'seo_favicon',
            'seo_default_og_image',
        ])->pluck('value', 'key')->toArray();

        // Check if we use form->fill or something else.
        // If the signature changes to Schema, InteractsWithForms might work differently.
        // But for now, let's keep it and see. The error was ONLY about the form() signature.
        $this->form->fill($settings);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Analytics & Tracking')
                    ->description('Connect your site to Google Analytics, Tag Manager, and Search Console.')
                    ->schema([
                        TextInput::make('seo_ga4_id')
                            ->label('Google Analytics 4 ID')
                            ->placeholder('G-XXXXXXXXXX')
                            ->helperText('Your GA4 Measurement ID.'),

                        TextInput::make('seo_gtm_id')
                            ->label('Google Tag Manager ID')
                            ->placeholder('GTM-XXXXXXX')
                            ->helperText('Your GTM Container ID.'),

                        TextInput::make('seo_gsc_verification')
                            ->label('Google Search Console Verification')
                            ->helperText('HTML Tag content (meta name="google-site-verification" content="YOUR_CODE"). Enter ONLY the code.'),
                    ])->columns(2),

                Section::make('Default Metadata')
                    ->description('Fallback meta tags if a page does not have specific SEO data.')
                    ->schema([
                        TextInput::make('seo_default_title')
                            ->label('Default Meta Title')
                            ->placeholder(config('app.name')),

                        Textarea::make('seo_default_description')
                            ->label('Default Meta Description')
                            ->rows(3),

                        \Filament\Forms\Components\FileUpload::make('seo_favicon')
                            ->label('Site Favicon')
                            ->image()
                            ->disk('public')
                            ->directory('seo')
                            ->helperText('Upload a square image (32x32 or 64x64 recommended).'),

                        \Filament\Forms\Components\FileUpload::make('seo_default_og_image')
                            ->label('Default OG Image (Social Share)')
                            ->image()
                            ->disk('public')
                            ->directory('seo')
                            ->helperText('Fallback image for social sharing (1200x630px recommended).'),
                    ]),
            ])
            ->statePath('data');
    }

    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['label' => $this->getLabelForKey($key), 'value' => $value]
            );
        }

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }

    protected function getLabelForKey($key): string
    {
        return match ($key) {
            'seo_ga4_id' => 'Google Analytics 4 ID',
            'seo_gtm_id' => 'Google Tag Manager ID',
            'seo_gsc_verification' => 'GSC Verification Code',
            'seo_default_title' => 'Default Meta Title',
            'seo_default_description' => 'Default Meta Description',
            'seo_favicon' => 'Site Favicon',
            'seo_default_og_image' => 'Default OG Image',
            default => ucwords(str_replace(['seo_', '_'], ['', ' '], $key)),
        };
    }
}
