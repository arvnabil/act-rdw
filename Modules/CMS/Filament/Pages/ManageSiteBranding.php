<?php

namespace Modules\CMS\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Modules\Settings\Models\Setting;

class ManageSiteBranding extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-swatch';
    protected static string | \UnitEnum | null $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Site Branding';
    protected static ?string $title = 'Site Branding Settings';
    protected string $view = 'filament.activioncms.pages.manage-site-branding';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::whereIn('key', [
            'site_logo_header',
            'site_logo_footer',
            'site_logo_icon',
            'instagram_username',
            'instagram_url',
            'facebook_url',
            'linkedin_url',
            'twitter_url',
            'youtube_url',
            'whatsapp_number',
            'office_hours',
            'header_button_text',
            'header_button_url',
            'site_logo_preloader',
        ])->pluck('value', 'key')->toArray();

        $this->form->fill($settings);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Logos')
                    ->description('Upload your site logos. SVG format is recommended for best quality.')
                    ->schema([
                        FileUpload::make('site_logo_header')
                            ->label('Header Logo')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->helperText('Displayed in the main header and desktop menu.')
                            ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string {
                                return \App\Helpers\UploadHelper::getSluggedFilename($file, 'site/logo-header');
                            }),

                        FileUpload::make('site_logo_footer')
                            ->label('Footer Logo')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->helperText('Displayed in the footer area.')
                            ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string {
                                return \App\Helpers\UploadHelper::getSluggedFilename($file, 'site/logo-footer');
                            }),

                        FileUpload::make('site_logo_icon')
                            ->label('Logo Icon (Mobile/Sidebar)')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->helperText('Small icon version for mobile menu and sidebars.')
                            ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string {
                                return \App\Helpers\UploadHelper::getSluggedFilename($file, 'site/logo-icon');
                            }),

                        FileUpload::make('site_logo_preloader')
                            ->label('Preloader Logo')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->helperText('Logo displayed during initial site loading.')
                            ->getUploadedFileNameForStorageUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file): string {
                                return \App\Helpers\UploadHelper::getSluggedFilename($file, 'site/logo-preloader');
                            }),
                    ])->columns(2),

                Section::make('General Information')
                    ->description('Contact and operational data shown across the site.')
                    ->schema([
                        TextInput::make('whatsapp_number')
                            ->label('WhatsApp Number')
                            ->placeholder('6285162994602')
                            ->helperText('Use format with country code, e.g. 628...'),

                        TextInput::make('office_hours')
                            ->label('Office Hours')
                            ->placeholder('Senin - Jumat: 8.30 am - 05.30 pm'),
                    ])->columns(2),

                Section::make('Social Media Links')
                    ->description('URLs for your social media profiles.')
                    ->schema([
                        TextInput::make('instagram_url')
                            ->label('Instagram URL')
                            ->url()
                            ->placeholder('https://www.instagram.com/activ_teknologi/'),

                        TextInput::make('instagram_username')
                            ->label('Instagram Username')
                            ->placeholder('@activ_teknologi'),

                        TextInput::make('facebook_url')
                            ->label('Facebook URL')
                            ->url(),

                        TextInput::make('linkedin_url')
                            ->label('LinkedIn URL')
                            ->url(),

                        TextInput::make('twitter_url')
                            ->label('Twitter URL')
                            ->url(),

                        TextInput::make('youtube_url')
                            ->label('YouTube URL')
                            ->url(),
                    ])->columns(3),

                Section::make('Header Action Button')
                    ->description('Configure the call-to-action button in the header.')
                    ->schema([
                        TextInput::make('header_button_text')
                            ->label('Button Text')
                            ->placeholder('Hubungi Kami'),

                        TextInput::make('header_button_url')
                            ->label('Button URL / Key')
                            ->placeholder('whatsapp')
                            ->helperText('Enter "whatsapp" to use WA link, or a custom URL / slug.'),
                    ])->columns(2),
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
            'site_logo_header' => 'Site Logo Header',
            'site_logo_footer' => 'Site Logo Footer',
            'site_logo_icon' => 'Site Logo Icon',
            'site_logo_preloader' => 'Preloader Logo',
            'instagram_username' => 'Instagram Username',
            'instagram_url' => 'Instagram URL',
            'office_hours' => 'Office Hours',
            default => ucwords(str_replace('_', ' ', $key)),
        };
    }
}
