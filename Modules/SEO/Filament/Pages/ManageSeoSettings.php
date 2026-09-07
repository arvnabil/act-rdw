<?php

namespace Modules\SEO\Filament\Pages;

use App\Helpers\UploadHelper;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Modules\Settings\Models\Setting;

class ManageSeoSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static string|\UnitEnum|null $navigationGroup = 'Seo Management';

    protected static ?string $navigationLabel = 'Global SEO';

    protected static ?string $title = 'Global SEO Settings';

    protected string $view = 'filament.activioncms.pages.manage-seo-settings';

    public ?array $data = [];

    /**
     * Server-side flag (never the credential itself) used to render a masked
     * "Configured" state for the Service Account JSON field.
     */
    public bool $serviceAccountConfigured = false;

    public function mount(): void
    {
        // Load existing settings. The Service Account JSON secret is deliberately
        // NOT loaded here so it never enters Livewire state / the browser.
        $settings = Setting::whereIn('key', [
            'seo_ga4_id',
            'seo_gtm_id',
            'seo_gsc_verification',
            'seo_default_title',
            'seo_default_description',
            'seo_favicon',
            'seo_default_og_image',
            'seo_ga4_property_id',
        ])->pluck('value', 'key')->toArray();

        $this->serviceAccountConfigured = Setting::query()
            ->where('key', 'seo_ga4_service_account_json')
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->exists();

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

                        FileUpload::make('seo_favicon')
                            ->label('Site Favicon')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->downloadable()
                            ->openable()
                            ->helperText('Nama file akan otomatis disesuaikan. Ukuran maks: 2MB.')
                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                return UploadHelper::getSluggedFilename($file, 'seo/favicon');
                            }),

                        FileUpload::make('seo_default_og_image')
                            ->label('Default OG Image (Social Share)')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->downloadable()
                            ->openable()
                            ->helperText('Nama file akan otomatis disesuaikan. Ukuran maks: 2MB.')
                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
                                return UploadHelper::getSluggedFilename($file, 'seo/og-default');
                            }),
                    ]),

                Section::make('Google Analytics Dashboard API')
                    ->description('Configuration for the Filament dashboard visual charts. This is separate from the Tracking ID above.')
                    ->schema([
                        TextInput::make('seo_ga4_property_id')
                            ->label('GA4 Property ID')
                            ->placeholder('123456789')
                            ->helperText('Get this from Admin > Property Settings > Property Details in GA4 Console.'),

                        Placeholder::make('service_account_status')
                            ->label('Service Account JSON Content')
                            ->content(fn (): string => $this->serviceAccountConfigured
                                ? 'Configured. The credential is stored encrypted and is never shown again. Leave the field below empty to keep it, or paste a new JSON key file to replace it.'
                                : 'Not configured yet. Paste the entire content of your Google Service Account JSON key file below.'),

                        Textarea::make('seo_ga4_service_account_json')
                            ->label('Replace Service Account JSON (optional)')
                            ->rows(10)
                            ->placeholder($this->serviceAccountConfigured
                                ? '•••••••••••••• (configured) — leave blank to keep the existing credential'
                                : '{"type": "service_account", ...}')
                            ->helperText('Only filled in when replacing the credential. The existing value is never loaded into this form.'),
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

        // Validate a replacement Service Account JSON before persisting anything.
        if (filled($data['seo_ga4_service_account_json'] ?? null)
            && ! $this->isValidServiceAccountJson($data['seo_ga4_service_account_json'])) {
            Notification::make()
                ->danger()
                ->title('Invalid Service Account JSON')
                ->body('The pasted value must be a valid JSON Google service account key file containing type, project_id, private_key and client_email.')
                ->send();

            return;
        }

        foreach ($data as $key => $value) {
            // Secret keys are handled explicitly: a blank value keeps the
            // existing credential. The stored secret is never returned to
            // the browser, and the model encrypts it at rest on write.
            if (Setting::isSecretKey($key)) {
                if (blank($value)) {
                    continue;
                }

                Setting::updateOrCreate(
                    ['key' => $key],
                    ['label' => $this->getLabelForKey($key), 'value' => $value]
                );

                continue;
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['label' => $this->getLabelForKey($key), 'value' => $value]
            );
        }

        $this->serviceAccountConfigured = Setting::query()
            ->where('key', 'seo_ga4_service_account_json')
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->exists();

        Notification::make()
            ->success()
            ->title(__('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }

    protected function isValidServiceAccountJson(string $json): bool
    {
        $decoded = json_decode($json, true);

        if (! is_array($decoded)) {
            return false;
        }

        foreach (['type', 'project_id', 'private_key', 'client_email'] as $requiredKey) {
            if (empty($decoded[$requiredKey])) {
                return false;
            }
        }

        return ($decoded['type'] ?? null) === 'service_account';
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
            'seo_ga4_property_id' => 'GA4 Property ID',
            'seo_ga4_service_account_json' => 'Service Account JSON',
            default => ucwords(str_replace(['seo_', '_'], ['', ' '], $key)),
        };
    }
}
