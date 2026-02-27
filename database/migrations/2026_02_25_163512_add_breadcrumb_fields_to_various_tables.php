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
        $tables = ['news', 'projects', 'brands', 'services', 'service_solutions', 'products'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'breadcrumb_image')) {
                        $table->text('breadcrumb_image')->nullable()->after('id');
                    }
                    if (!Schema::hasColumn($table->getTable(), 'show_breadcrumb')) {
                        $table->boolean('show_breadcrumb')->default(true)->after('breadcrumb_image');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['news', 'projects', 'brands', 'services', 'service_solutions', 'products'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropColumn(['breadcrumb_image', 'show_breadcrumb']);
                });
            }
        }
    }
};
