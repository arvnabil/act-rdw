<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // Note: services table is in ServiceSolutions module, ensure that migration runs first.
            // If strict foreign key fails, we might need to rely on logical link or Move Services to Core too.
            // For now, assuming ServiceSolutions migration runs before this date.
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('price', 15, 2)->nullable(); // Added price
            $table->string('link_accommerce')->nullable(); // Marketplace link (Accocommerce)

            $table->string('sku')->nullable();
            $table->string('solution_type')->nullable();
            $table->string('datasheet_url')->nullable();
            $table->json('tags')->nullable();
            $table->json('specs')->nullable();
            $table->text('specification_text')->nullable();
            $table->json('features')->nullable();
            $table->text('features_text')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot table for products <-> configurator_criteria (Legacy/Pivot)
        // If this pivot is used by ServiceSolutions, it should technically be there?
        // But it links Product (Core) to ConfiguratorCriteria (ServiceSolutions).
        // Let's keep the pivot here or in ServiceSolutions?
        // Original file had it. I will keep it here to preserve original migration login,
        // OR leave it in the original file if I only remove 'products' table.
        // I will leave 'product_criteria' in the original file because it links to 'configurator_criteria' which I am NOT moving.
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
