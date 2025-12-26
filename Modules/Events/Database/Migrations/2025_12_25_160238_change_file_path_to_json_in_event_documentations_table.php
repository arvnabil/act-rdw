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
        Schema::table('event_documentations', function (Blueprint $table) {
            // Check if dbal is installed, otherwise drop/add
            // Actually, to be safe without dbal dependence for now, let's drop and adding if it was text/string,
            // but wait, Laravel 11+ might handle this better.
            // Let's assume standard behavior.
            // Alternatively, just make it text/json.
            // On sqlite, json is text.

            // BETTER APPROACH for maximum compatibility without knowing DB driver details:
            // Just use text, and cast to array in model. JSON column type is alias for json on some, text on others.
             $table->text('file_path')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_documentations', function (Blueprint $table) {
            $table->string('file_path')->change();
        });
    }
};
