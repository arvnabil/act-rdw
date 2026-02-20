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
        // 1. Pivot Table for Projects and Brands
        if (!Schema::hasTable('project_brand')) {
            Schema::create('project_brand', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->cascadeOnDelete();
                $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        // 2. Pivot Table for Projects and Service Solutions
        if (!Schema::hasTable('project_service_solution')) {
            Schema::create('project_service_solution', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->cascadeOnDelete();
                $table->foreignId('service_solution_id')->constrained('service_solutions')->cascadeOnDelete();
                $table->timestamps();
            });
        }

        // 3. Add tags column to projects
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'tags')) {
                $table->json('tags')->nullable()->after('whatsapp_note');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'tags')) {
                $table->dropColumn('tags');
            }
        });
        Schema::dropIfExists('project_service_solution');
        Schema::dropIfExists('project_brand');
    }
};