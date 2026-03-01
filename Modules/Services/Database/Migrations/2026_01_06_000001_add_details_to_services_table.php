<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('thumbnail')->nullable()->after('description');
            $table->string('icon')->nullable()->after('thumbnail');
            $table->string('hero_subtitle')->nullable()->after('icon');
            $table->string('grid_title')->nullable()->after('hero_subtitle');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['thumbnail', 'icon', 'hero_subtitle', 'grid_title']);
        });
    }
};
