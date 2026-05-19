<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // 1. Force convert table to utf8mb4 to support emojis before modification
            DB::statement('ALTER TABLE seo_meta CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

            // 2. Modify columns to TEXT
            DB::statement('ALTER TABLE seo_meta MODIFY COLUMN title TEXT NULL');
            DB::statement('ALTER TABLE seo_meta MODIFY COLUMN keywords TEXT NULL');
            DB::statement('ALTER TABLE seo_meta MODIFY COLUMN og_title TEXT NULL');
            DB::statement('ALTER TABLE seo_meta MODIFY COLUMN og_image TEXT NULL');
            DB::statement('ALTER TABLE seo_meta MODIFY COLUMN canonical_url TEXT NULL');
        } else {
            // SQLite compatibility fallback
            Schema::table('seo_meta', function (Blueprint $table) {
                $table->text('title')->nullable()->change();
                $table->text('keywords')->nullable()->change();
                $table->text('og_title')->nullable()->change();
                $table->text('og_image')->nullable()->change();
                $table->text('canonical_url')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE seo_meta MODIFY COLUMN title VARCHAR(191) NULL');
            DB::statement('ALTER TABLE seo_meta MODIFY COLUMN keywords VARCHAR(191) NULL');
            DB::statement('ALTER TABLE seo_meta MODIFY COLUMN og_title VARCHAR(191) NULL');
            DB::statement('ALTER TABLE seo_meta MODIFY COLUMN og_image VARCHAR(191) NULL');
            DB::statement('ALTER TABLE seo_meta MODIFY COLUMN canonical_url VARCHAR(191) NULL');
        } else {
            Schema::table('seo_meta', function (Blueprint $table) {
                $table->string('title', 191)->nullable()->change();
                $table->string('keywords', 191)->nullable()->change();
                $table->string('og_title', 191)->nullable()->change();
                $table->string('og_image', 191)->nullable()->change();
                $table->string('canonical_url', 191)->nullable()->change();
            });
        }
    }
};
