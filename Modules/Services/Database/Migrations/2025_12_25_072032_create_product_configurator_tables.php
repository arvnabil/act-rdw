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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurator_criteria');
        Schema::dropIfExists('services');
    }
};
