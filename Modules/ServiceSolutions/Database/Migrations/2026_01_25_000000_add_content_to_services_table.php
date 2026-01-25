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
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'content')) {
                $table->longText('content')->nullable()->after('grid_title');
            }
            if (!Schema::hasColumn('services', 'excerpt')) {
                $table->text('excerpt')->nullable()->after('content');
            }
            if (!Schema::hasColumn('services', 'featured_image')) {
                $table->string('featured_image')->nullable()->after('excerpt');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['content', 'excerpt', 'featured_image']);
        });
    }
};
