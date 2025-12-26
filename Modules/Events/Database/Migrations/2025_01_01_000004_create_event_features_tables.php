<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add is_certificate_available to events table
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_certificate_available')->default(false)->after('is_active');
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('event_user_id')->nullable()->constrained('event_users')->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('pending'); // pending, paid, rejected, cancelled
            $table->string('payment_method')->nullable();
            $table->string('payment_proof')->nullable(); // path to image
            $table->timestamps();
        });

        Schema::create('event_documentations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('type')->default('image'); // image, presentation, video_link
            $table->string('file_path'); // or url for video
            $table->text('caption')->nullable();
            $table->timestamps();
        });

        Schema::create('event_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('certificate_background')->nullable(); // Background image path
            $table->string('content_layout')->nullable(); // JSON for coordinate/text positions
            $table->string('signature')->nullable(); // Signature image path
            $table->string('signer_name')->nullable();
            $table->string('signer_position')->nullable();
            $table->timestamps();
        });

        Schema::create('event_user_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_certificate_id')->nullable()->constrained('event_certificates')->nullOnDelete();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('event_user_id')->constrained('event_users')->cascadeOnDelete();
            $table->string('certificate_code')->unique();
            $table->string('file_path')->nullable();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_certificates');
        Schema::dropIfExists('event_documentations');
        Schema::dropIfExists('event_registrations');

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('is_certificate_available');
        });
    }
};
