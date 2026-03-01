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
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->string('og_title')->nullable()->after('keywords');
            $table->text('og_description')->nullable()->after('og_title');
            $table->string('canonical_url')->nullable()->after('og_description');
            $table->boolean('noindex')->default(false)->after('canonical_url');
            $table->json('schema_override')->nullable()->after('schema_markup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            //
        });
    }
};
