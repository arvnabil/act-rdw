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
        Schema::table('news', function (Blueprint $table) {
            if (!Schema::hasColumn('news', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
