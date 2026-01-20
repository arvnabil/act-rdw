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
        Schema::create('form_security_settings', function (Blueprint $table) {
            $table->string('form_key')->primary();
            $table->boolean('captcha_enabled')->default(true);
            $table->boolean('auto_managed')->default(true);
            $table->timestamp('last_decision_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_security_settings');
    }
};
