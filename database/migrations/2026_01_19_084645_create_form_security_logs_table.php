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
        Schema::create('form_security_logs', function (Blueprint $table) {
            $table->id();
            $table->string('form_key')->index();
            $table->string('page_slug')->nullable();
            $table->float('captcha_score')->nullable();
            $table->string('decision'); // allow | block | honeypot
            $table->string('reason')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_security_logs');
    }
};
