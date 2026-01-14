<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Force cleanup for dev stability
        Schema::dropIfExists('product_configurator_option');
        Schema::dropIfExists('configurator_options');
        Schema::dropIfExists('configurator_questions');
        Schema::dropIfExists('configurator_steps');
        Schema::dropIfExists('configurators');

        // 1. Configurators (The Wizard Container)
        Schema::create('configurators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            // Optional: You might want 'is_active' or similar
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Steps
        Schema::create('configurator_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('configurator_id')->constrained()->cascadeOnDelete();
            $table->string('name')->comment('Stepper Label');
            $table->string('title')->nullable()->comment('Page Title');
            $table->text('description')->nullable();
            $table->string('layout')->default('default'); // Default, split_visual
            $table->string('image')->nullable(); // Visualizer asset
            $table->integer('sort_order')->default(0);
            $table->json('conditions')->nullable();
            $table->timestamps();
        });

        // 3. Questions
        Schema::create('configurator_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('step_id')->constrained('configurator_steps')->cascadeOnDelete();
            $table->string('label');
            $table->string('variable_name');
            $table->string('type')->default('card_selection');
            $table->boolean('is_mandatory')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('conditions')->nullable();
            $table->timestamps();
        });

        // 4. Options
        Schema::create('configurator_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('configurator_questions')->cascadeOnDelete();
            $table->foreignId('service_solution_id')->nullable()->constrained('service_solutions')->onDelete('set null');
            $table->string('label');
            $table->string('value');
            $table->json('metadata')->nullable();
            $table->integer('sort_order')->default(0);
            $table->json('conditions')->nullable();
            $table->timestamps();
        });

        // 5. Product Link (Bridge between Option and Product)
        Schema::create('product_configurator_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('configurator_option_id')->constrained('configurator_options')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_configurator_option');
        Schema::dropIfExists('configurator_options');
        Schema::dropIfExists('configurator_questions');
        Schema::dropIfExists('configurator_steps');
        Schema::dropIfExists('configurators');
    }
};
