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
        // Cleanup old tables if they exist or force fresh state
        Schema::dropIfExists('product_configurator_option');
        Schema::dropIfExists('configurator_options');
        Schema::dropIfExists('configurator_questions');
        Schema::dropIfExists('configurator_steps');
        Schema::dropIfExists('configurators');

        Schema::create('configurators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('configurator_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('configurator_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Internal/Stepper name
            $table->string('title')->nullable(); // Display title
            $table->text('description')->nullable();
            $table->string('layout')->default('default'); // default, split_visual
            $table->string('image')->nullable(); // Visualizer image/asset path
            $table->integer('sort_order')->default(0);
            $table->json('conditions')->nullable();
            $table->timestamps();
        });

        Schema::create('configurator_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('step_id')->constrained('configurator_steps')->cascadeOnDelete();
            $table->string('label');
            $table->string('variable_name');
            $table->string('type'); // e.g. card_selection, quantity_input
            $table->boolean('is_mandatory')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('conditions')->nullable();
            $table->timestamps();
        });

        Schema::create('configurator_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('configurator_questions')->cascadeOnDelete();
            $table->string('label');
            $table->string('value');
            $table->json('metadata')->nullable(); // price, image, etc.
            $table->integer('sort_order')->default(0);
            $table->json('conditions')->nullable();
            $table->timestamps();
        });

        Schema::create('product_configurator_option', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('configurator_option_id')->constrained('configurator_options')->cascadeOnDelete();
            $table->primary(['product_id', 'configurator_option_id'], 'prod_conf_opt_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_configurator_option');
        Schema::dropIfExists('configurator_options');
        Schema::dropIfExists('configurator_questions');
        Schema::dropIfExists('configurator_steps');
        Schema::dropIfExists('configurators');
    }
};
