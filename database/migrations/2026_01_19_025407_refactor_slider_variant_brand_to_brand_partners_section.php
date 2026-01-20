<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $sections = DB::table('page_sections')
            ->where('section_key', 'slider')
            ->get();

        foreach ($sections as $section) {
            $config = json_decode($section->config, true);

            // Check if this slider is a 'brand' variant
            if (isset($config['variant']) && $config['variant'] === 'brand') {

                $newConfig = [
                    'title' => $config['title'] ?? '',
                    'subtitle' => $config['subtitle'] ?? '',
                    'brands' => []
                ];

                // Transform 'slides' to 'brands'
                if (isset($config['slides']) && is_array($config['slides'])) {
                    foreach ($config['slides'] as $slide) {
                        $newConfig['brands'][] = [
                            'image' => $slide['image'] ?? '',
                            'url' => $slide['url'] ?? '#',
                            'name' => $slide['title'] ?? '', // Map title to name if available
                        ];
                    }
                }

                DB::table('page_sections')
                    ->where('id', $section->id)
                    ->update([
                        'section_key' => 'brand_partners',
                        'config' => json_encode($newConfig)
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_partners_section', function (Blueprint $table) {
            //
        });
    }
};
