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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('client')->nullable();
            $table->string('category')->nullable();
            $table->date('project_date')->nullable();
            $table->text('address')->nullable();
            $table->json('download_brochures')->nullable(); // Stores array of file paths
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['client', 'category', 'project_date', 'address', 'download_brochures']);
        });
    }
};
