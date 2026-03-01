<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_solution_category', function (Blueprint $table) {
            $table->foreignId('service_solution_id')->constrained('service_solutions')->onDelete('cascade');
            $table->foreignId('service_category_id')->constrained('service_categories')->onDelete('cascade');
            $table->primary(['service_solution_id', 'service_category_id'], 'solution_category_primary');
        });

        Schema::create('service_solution_brand', function (Blueprint $table) {
            $table->foreignId('service_solution_id')->constrained('service_solutions')->onDelete('cascade');
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->primary(['service_solution_id', 'brand_id'], 'solution_brand_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_solution_brand');
        Schema::dropIfExists('service_solution_category');
    }
};
