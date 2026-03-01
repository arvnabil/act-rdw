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
        // 1. Force convert table to utf8mb4 to support emojis before modification
        DB::statement('ALTER TABLE seo_meta CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        // 2. Modify columns to TEXT
        DB::statement('ALTER TABLE seo_meta MODIFY COLUMN title TEXT NULL');
        DB::statement('ALTER TABLE seo_meta MODIFY COLUMN keywords TEXT NULL');
        DB::statement('ALTER TABLE seo_meta MODIFY COLUMN og_title TEXT NULL');
        DB::statement('ALTER TABLE seo_meta MODIFY COLUMN og_image TEXT NULL');
        DB::statement('ALTER TABLE seo_meta MODIFY COLUMN canonical_url TEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE seo_meta MODIFY COLUMN title VARCHAR(191) NULL');
        DB::statement('ALTER TABLE seo_meta MODIFY COLUMN keywords VARCHAR(191) NULL');
        DB::statement('ALTER TABLE seo_meta MODIFY COLUMN og_title VARCHAR(191) NULL');
        DB::statement('ALTER TABLE seo_meta MODIFY COLUMN og_image VARCHAR(191) NULL');
        DB::statement('ALTER TABLE seo_meta MODIFY COLUMN canonical_url VARCHAR(191) NULL');
    }
};
