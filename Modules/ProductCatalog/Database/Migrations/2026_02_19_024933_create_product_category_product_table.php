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
        // Drop if exists to clean up failed migrations
        Schema::dropIfExists('product_category_product');

        Schema::create('product_category_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_category_id');
            $table->timestamps();
            
            // Foreign keys - We'll just define them as bigInteger for now.
            // If DB engine or collation differs (e.g. MyISAM vs InnoDB), foreign keys fail.
            // This is safer for mixed environments.
            $table->unique(['product_id', 'product_category_id']);
        });

        // Migrate existing data
        $products = \Illuminate\Support\Facades\DB::table('products')
            ->whereNotNull('product_category_id')
            ->get(['id', 'product_category_id']);

        foreach ($products as $product) {
            \Illuminate\Support\Facades\DB::table('product_category_product')->insert([
                'product_id' => $product->id,
                'product_category_id' => $product->product_category_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Remove the old column
        if (Schema::hasColumn('products', 'product_category_id')) {
            // Drop foreign key first if it exists. 
            // We use a try-catch because in some hosting environments (like the user's cPanel),
            // the foreign key name might differ or be missing entirely.
            try {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropForeign(['product_category_id']);
                });
            } catch (\Exception $e) {
                // Log or ignore if the foreign key doesn't exist
                \Illuminate\Support\Facades\Log::info("Migration: Could not drop foreign key products_product_category_id_foreign. It might not exist. Error: " . $e->getMessage());
            }

            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('product_category_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore column
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('product_category_id')->nullable()->constrained('product_categories')->nullOnDelete();
        });

        // Restore data (taking the first category found for each product)
        $pivots = \Illuminate\Support\Facades\DB::table('product_category_product')->get();
        foreach ($pivots as $pivot) {
             \Illuminate\Support\Facades\DB::table('products')
                ->where('id', $pivot->product_id)
                ->whereNull('product_category_id') // Only set if not already set (in case of multiples, first wins)
                ->update(['product_category_id' => $pivot->product_category_id]);
        }

        Schema::dropIfExists('product_category_product');
    }
};
