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
        Schema::create('form_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('form_key')->index(); // e.g. 'contact_section'
            $table->string('page_slug')->nullable();
            $table->ipAddress('ip_address')->nullable(); // To calculate unique views approx
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_analytics');
    }
};
