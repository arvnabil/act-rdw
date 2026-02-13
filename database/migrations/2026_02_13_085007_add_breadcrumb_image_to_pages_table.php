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
        if (!Schema::hasColumn('pages', 'breadcrumb_image')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->string('breadcrumb_image')->nullable()->after('show_breadcrumb');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pages', 'breadcrumb_image')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('breadcrumb_image');
            });
        }
    }
};
