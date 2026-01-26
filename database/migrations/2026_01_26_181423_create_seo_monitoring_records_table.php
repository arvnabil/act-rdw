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
        Schema::create('seo_monitoring_records', function (Blueprint $table) {
            $table->id();
            $table->string('url')->unique();
            $table->string('path');
            $table->string('model')->nullable();
            $table->string('model_id')->nullable();
            $table->boolean('is_noindex')->default(false);
            $table->boolean('in_sitemap')->default(false);
            $table->boolean('canonical_valid')->default(false);
            $table->integer('seo_score')->default(0);
            $table->float('priority')->default(0.5);
            $table->string('changefreq')->default('weekly');
            $table->timestamp('last_modified')->nullable();
            $table->timestamps();

            $table->index(['model', 'model_id']);
            $table->index('is_noindex');
            $table->index('in_sitemap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_monitoring_records');
    }
};
