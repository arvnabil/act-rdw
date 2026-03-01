<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            [
                'key' => 'header_button_text',
                'label' => 'Header Button Text',
                'value' => 'Hubungi Kami',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'header_button_url',
                'label' => 'Header Button URL',
                'value' => 'whatsapp',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'header_button_visible',
                'label' => 'Header Button Visible (1/0)',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            \Modules\Settings\Models\Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Modules\Settings\Models\Setting::whereIn('key', [
            'header_button_text',
            'header_button_url',
            'header_button_visible'
        ])->delete();
    }
};
