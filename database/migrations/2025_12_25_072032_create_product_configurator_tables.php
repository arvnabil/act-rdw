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
        // 1. Brands Table
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_path')->nullable();
            $table->string('website_url')->nullable();
            $table->timestamps();
        });

        // 2. Services Table (Room, Server, Surveillance)
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. Configurator Criteria (Options like 'Small Room', 'Dome Camera')
        Schema::create('configurator_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('type'); // e.g., 'room_size', 'camera_type'
            $table->string('value'); // e.g., 'small', 'dome'
            $table->string('label'); // Display Name
            $table->string('icon_path')->nullable();
            $table->timestamps();
        });

        // 4. Products Table
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('sku')->nullable();
            $table->string('solution_type')->nullable();
            $table->string('datasheet_url')->nullable();
            $table->json('tags')->nullable();
            $table->json('specs')->nullable(); // stored as key-value pairs
            $table->text('specification_text')->nullable();
            $table->json('features')->nullable(); // stored as array of objects
            $table->text('features_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Product Criteria Pivot (Linking Products to Configurator Options)
        Schema::create('product_criteria', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('configurator_criteria_id')->constrained('configurator_criteria')->onDelete('cascade');
            $table->primary(['product_id', 'configurator_criteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_criteria');
        Schema::dropIfExists('products');
        Schema::dropIfExists('configurator_criteria');
        Schema::dropIfExists('services');
        Schema::dropIfExists('brands');
    }
};
