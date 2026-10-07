<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint ) {
            ->string('avatar')->nullable()->after('email');
            ->string('phone')->nullable()->after('avatar');
            ->boolean('is_active')->default(true)->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint ) {
            ->dropColumn(['avatar', 'phone', 'is_active']);
        });
    }
};
