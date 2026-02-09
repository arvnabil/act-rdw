<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class LocalBusinessSettingsSeeder extends Seeder
{
    /**
     * Seed the settings table with LocalBusiness data for SEO Schema.
     */
    public function run(): void
    {
        $settings = [
            // Company Identity
            ['key' => 'company_name', 'value' => 'ACTiV Technology'],
            ['key' => 'company_tagline', 'value' => 'Digital Transformation Partner'],
            
            // Contact Information
            ['key' => 'company_phone', 'value' => '+62 21 1234 5678'],
            ['key' => 'company_email', 'value' => 'info@activ.co.id'],
            ['key' => 'company_whatsapp', 'value' => '+62 812 3456 7890'],
            
            // Address
            ['key' => 'company_address', 'value' => 'Jl. Sudirman No. 123, Lantai 5'],
            ['key' => 'company_city', 'value' => 'Jakarta Selatan'],
            ['key' => 'company_province', 'value' => 'DKI Jakarta'],
            ['key' => 'company_postal_code', 'value' => '12190'],
            ['key' => 'company_country', 'value' => 'Indonesia'],
            
            // Branding
            ['key' => 'company_logo', 'value' => 'logos/activ-logo.png'],
            
            // Social Media (for Organization sameAs)
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/activtech'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/activtech'],
            ['key' => 'social_linkedin', 'value' => 'https://linkedin.com/company/activtech'],
            ['key' => 'social_twitter', 'value' => 'https://twitter.com/activtech'],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com/@activtech'],
            
            // Opening Hours (JSON format for flexibility)
            ['key' => 'company_opening_hours', 'value' => json_encode([
                ['days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'], 'opens' => '09:00', 'closes' => '17:00'],
            ])],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        $this->command->info('LocalBusiness settings seeded successfully!');
    }
}
