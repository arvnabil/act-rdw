<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'seo_default_title',
                'value' => 'ACTiV - Digital Solutions',
                'label' => 'Default Meta Title',
                'type' => 'text',
            ],
            [
                'key' => 'seo_default_description',
                'value' => 'Empowering businesses with modern digital solutions.',
                'label' => 'Default Meta Description',
                'type' => 'textarea',
            ],
            [
                'key' => 'seo_default_og_image',
                'value' => null,
                'label' => 'Default OG Image',
                'type' => 'image',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
