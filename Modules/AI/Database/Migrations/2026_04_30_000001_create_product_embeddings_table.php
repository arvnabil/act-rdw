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
        // Cek apakah koneksi pgsql_vector tersedia dan bertipe pgsql
        try {
            $connection = DB::connection('pgsql_vector');
            $driver = $connection->getDriverName();
            
            if ($driver !== 'pgsql') {
                return; // Lewati jika bukan PostgreSQL
            }

            // Jalankan hanya jika di PostgreSQL
            $connection->statement('CREATE EXTENSION IF NOT EXISTS vector');

            Schema::connection('pgsql_vector')->create('product_embeddings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->vector('embedding', 3072);
                $table->text('content');
                $table->jsonb('metadata')->nullable();
                $table->timestamps();
            });
        } catch (\Exception $e) {
            // Jika koneksi tidak ada, lewati saja migrasi ini
            return;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::connection('pgsql_vector')->dropIfExists('product_embeddings');
        } catch (\Exception $e) {
            // Ignore
        }
    }
};
