<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('organizers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('logo')->nullable();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('event_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->text('location')->nullable();
            $table->text('thumbnail')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('event_category_id')->nullable()->constrained('event_categories')->nullOnDelete();
            $table->decimal('price', 12, 2)->default(0);
            $table->integer('quota')->default(0);
            $table->json('speakers')->nullable(); // Array of {name, role, image}
            $table->text('map_url')->nullable();
            $table->text('youtube_link')->nullable();
            $table->text('meeting_link')->nullable();
            $table->json('schedule')->nullable();
            $table->json('faq')->nullable();
            $table->string('event_type')->default('Regular');
            $table->foreignId('organizer_id')->nullable()->constrained('organizers')->nullOnDelete()->after('event_category_id');
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
