<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\Filament\ActivioncmsPanelProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Modules\SEO\Filament\Pages\ManageSeoSettings;
use Modules\Settings\Filament\Resources\SettingResource\Pages\EditSetting;
use Modules\Settings\Models\Setting;
use Tests\TestCase;

class SettingsSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected string $serviceAccountJson;

    protected string $recaptchaSecret = 'recaptcha-secret-value-XYZ';

    protected function setUp(): void
    {
        parent::setUp();

        // Run main migrations first
        $this->artisan('migrate');

        // Load all module migrations dynamically
        $moduleDirs = glob(base_path('Modules/*/Database/Migrations'));
        foreach ($moduleDirs as $dir) {
            $relativePath = str_replace(base_path().'/', '', $dir);
            $this->artisan('migrate', ['--path' => $relativePath]);
        }

        $this->serviceAccountJson = json_encode([
            'type' => 'service_account',
            'project_id' => 'activ-test-project',
            'private_key' => "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDsTESTKEYONLY\n-----END PRIVATE KEY-----\n",
            'client_email' => 'ga-viewer@activ-test-project.iam.gserviceaccount.com',
            'token_uri' => 'https://oauth2.googleapis.com/token',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    protected function seedSensitiveSettings(): void
    {
        Setting::updateOrCreate(
            ['key' => 'seo_ga4_service_account_json'],
            ['label' => 'Service Account JSON', 'value' => $this->serviceAccountJson]
        );

        Setting::updateOrCreate(
            ['key' => 'recaptcha_secret_key'],
            ['label' => 'reCAPTCHA Secret Key', 'value' => $this->recaptchaSecret]
        );
    }

    protected function seedPublicSettings(): void
    {
        Setting::updateOrCreate(['key' => 'recaptcha_site_key'], ['label' => 'Site Key', 'value' => 'public-site-key-ABC']);
        Setting::updateOrCreate(['key' => 'whatsapp_number'], ['label' => 'WhatsApp', 'value' => '628123456789']);
        Setting::updateOrCreate(['key' => 'vion_is_active'], ['label' => 'Vion', 'value' => '1']);
        Setting::updateOrCreate(['key' => 'vion_welcome_message'], ['label' => 'Welcome', 'value' => 'Halo']);
        Setting::updateOrCreate(['key' => 'seo_ga4_id'], ['label' => 'GA4', 'value' => 'G-TEST123']);
        Setting::updateOrCreate(['key' => 'site_logo_header'], ['label' => 'Logo', 'value' => 'logos/header.svg']);
    }

    protected function extractInertiaSettings(string $html): array
    {
        preg_match('#data-page="([^"]+)"#', $html, $matches);

        $this->assertNotEmpty($matches, 'Inertia data-page payload not found in response.');

        $payload = json_decode(html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5), true);

        $this->assertIsArray($payload, 'Could not decode the Inertia data-page payload.');

        return $payload['props']['settings'] ?? [];
    }

    public function test_public_homepage_does_not_expose_secret_settings(): void
    {
        $this->seedSensitiveSettings();
        $this->seedPublicSettings();

        $response = $this->get('/');

        $response->assertOk();

        // Secret markers must never appear in the raw HTML.
        $response->assertDontSee('BEGIN PRIVATE KEY');
        $response->assertDontSee('MIIEvQIBADANBg');
        $response->assertDontSee('seo_ga4_service_account_json');
        $response->assertDontSee('recaptcha_secret_key');
        $response->assertDontSee('client_email');
        $response->assertDontSee($this->recaptchaSecret);

        // Public values must keep working (site key is rendered by the head).
        $response->assertSee('public-site-key-ABC');

        // The shared settings prop must only contain approved public keys.
        $settings = $this->extractInertiaSettings($response->getContent());

        $this->assertArrayHasKey('whatsapp_number', $settings);
        $this->assertArrayHasKey('recaptcha_site_key', $settings);
        $this->assertArrayNotHasKey('seo_ga4_service_account_json', $settings);
        $this->assertArrayNotHasKey('recaptcha_secret_key', $settings);

        foreach (array_keys($settings) as $key) {
            $baseKey = str_ends_with($key, '_rel') ? substr($key, 0, -4) : $key;
            $this->assertContains(
                $baseKey,
                Setting::publicKeys(),
                "Setting [{$key}] leaked into the Inertia payload but is not on the public allow-list."
            );
        }
    }

    public function test_secret_settings_are_encrypted_at_rest_and_server_side_only(): void
    {
        $this->seedSensitiveSettings();

        // Stored ciphertext must not contain the plaintext secret.
        $stored = DB::table('settings')->where('key', 'seo_ga4_service_account_json')->value('value');

        $this->assertStringStartsWith('encrypted:', (string) $stored);
        $this->assertStringNotContainsString('BEGIN PRIVATE KEY', (string) $stored);

        // A generic pluck of every row must never return the plaintext secret.
        $dump = Setting::pluck('value', 'key')->toArray();
        $this->assertStringNotContainsString('BEGIN PRIVATE KEY', json_encode($dump));

        // Server-side lookup still resolves the decrypted secret.
        $this->assertSame($this->serviceAccountJson, Setting::getValue('seo_ga4_service_account_json'));
        $this->assertSame($this->recaptchaSecret, Setting::getValue('recaptcha_secret_key'));
    }

    public function test_public_settings_helper_never_returns_secrets(): void
    {
        $this->seedSensitiveSettings();
        $this->seedPublicSettings();

        $public = Setting::getPublicSettings();

        $this->assertArrayHasKey('recaptcha_site_key', $public);
        $this->assertArrayNotHasKey('recaptcha_secret_key', $public);
        $this->assertArrayNotHasKey('seo_ga4_service_account_json', $public);
        $this->assertArrayNotHasKey('seo_ga4_property_id', $public);
    }

    public function test_admin_global_seo_page_does_not_expose_service_account_json(): void
    {
        $this->seedSensitiveSettings();
        $this->seedPublicSettings();

        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(ManageSeoSettings::getUrl(panel: 'activioncms'));

        $response->assertOk();
        $response->assertDontSee('BEGIN PRIVATE KEY');
        $response->assertDontSee('MIIEvQIBADANBg');
        $response->assertDontSee('client_email');
        $response->assertDontSee('activ-test-project');
        $response->assertSee('Configured');
    }

    public function test_admin_global_seo_blank_service_account_keeps_existing_credential(): void
    {
        $this->seedSensitiveSettings();

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageSeoSettings::class)
            ->assertSet('serviceAccountConfigured', true)
            ->set('data.seo_ga4_service_account_json', '')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('serviceAccountConfigured', true);

        // Existing credential untouched.
        $this->assertSame($this->serviceAccountJson, Setting::getValue('seo_ga4_service_account_json'));
    }

    public function test_admin_global_seo_new_service_account_json_is_saved_and_encrypted(): void
    {
        $this->seedSensitiveSettings();

        $replacement = json_encode([
            'type' => 'service_account',
            'project_id' => 'activ-replacement',
            'private_key' => "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgREPLACEMENTKEY\n-----END PRIVATE KEY-----\n",
            'client_email' => 'new@activ-replacement.iam.gserviceaccount.com',
            'token_uri' => 'https://oauth2.googleapis.com/token',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageSeoSettings::class)
            ->set('data.seo_ga4_service_account_json', $replacement)
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('serviceAccountConfigured', true);

        $this->assertSame($replacement, Setting::getValue('seo_ga4_service_account_json'));

        $stored = DB::table('settings')->where('key', 'seo_ga4_service_account_json')->value('value');
        $this->assertStringStartsWith('encrypted:', (string) $stored);
    }

    public function test_admin_global_seo_rejects_invalid_service_account_json(): void
    {
        $this->seedSensitiveSettings();

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ManageSeoSettings::class)
            ->set('data.seo_ga4_service_account_json', '{not-valid-json')
            ->call('save');

        // Invalid replacement must not overwrite the existing credential.
        $this->assertSame($this->serviceAccountJson, Setting::getValue('seo_ga4_service_account_json'));
    }

    public function test_ga4_configuration_is_wired_from_encrypted_db_credentials(): void
    {
        $this->seedSensitiveSettings();

        Setting::updateOrCreate(
            ['key' => 'seo_ga4_property_id'],
            ['label' => 'GA4 Property ID', 'value' => '339645953']
        );

        config(['services.recaptcha.secret' => null]);

        $provider = app()->getProvider(ActivioncmsPanelProvider::class);
        $this->assertNotNull($provider);

        $provider->boot();

        $this->assertSame('339645953', config('analytics.property_id'));

        $credentials = config('analytics.service_account_credentials_json');
        $this->assertIsArray($credentials);
        $this->assertStringContainsString('BEGIN PRIVATE KEY', $credentials['private_key'] ?? '');

        $this->assertSame($this->recaptchaSecret, config('services.recaptcha.secret'));
    }

    public function test_generic_settings_edit_never_loads_secret_value_into_livewire(): void
    {
        $this->seedSensitiveSettings();

        $user = User::factory()->create();
        $record = Setting::where('key', 'seo_ga4_service_account_json')->firstOrFail();

        Livewire::actingAs($user)
            ->test(EditSetting::class, ['record' => $record->getKey()])
            ->assertOk()
            ->assertSet('data.value', null)
            ->assertDontSee('BEGIN PRIVATE KEY');
    }

    public function test_generic_settings_edit_blank_save_keeps_secret_and_new_value_replaces(): void
    {
        $this->seedSensitiveSettings();

        $user = User::factory()->create();
        $record = Setting::where('key', 'recaptcha_secret_key')->firstOrFail();

        // Blank submission keeps the existing credential.
        Livewire::actingAs($user)
            ->test(EditSetting::class, ['record' => $record->getKey()])
            ->assertSet('data.value', null)
            ->set('data.label', 'reCAPTCHA Secret (updated)')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame($this->recaptchaSecret, Setting::getValue('recaptcha_secret_key'));

        // A real replacement value is persisted and encrypted.
        Livewire::actingAs($user)
            ->test(EditSetting::class, ['record' => $record->getKey()])
            ->assertSet('data.value', null)
            ->set('data.value', 'new-recaptcha-secret-456')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('new-recaptcha-secret-456', Setting::getValue('recaptcha_secret_key'));

        $stored = DB::table('settings')->where('key', 'recaptcha_secret_key')->value('value');
        $this->assertStringStartsWith('encrypted:', (string) $stored);
    }

    public function test_generic_settings_edit_public_value_still_round_trips(): void
    {
        $this->seedPublicSettings();

        $user = User::factory()->create();
        $record = Setting::where('key', 'whatsapp_number')->firstOrFail();

        Livewire::actingAs($user)
            ->test(EditSetting::class, ['record' => $record->getKey()])
            ->assertOk()
            ->assertSet('data.value', '628123456789')
            ->set('data.value', '628111111111')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('628111111111', Setting::getValue('whatsapp_number'));
    }
}
