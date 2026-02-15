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
        Schema::create('analytics_click_events', function (Blueprint $table) {
            $table->id();

            // Event Identity
            $table->string('event_type')->index(); // whatsapp, call, share, form_submit, download
            $table->string('event_label')->nullable(); 

            // Marketing Attribution (UTM)
            $table->string('utm_source')->nullable()->index();
            $table->string('utm_medium')->nullable()->index();
            $table->string('utm_campaign')->nullable()->index();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();

            // Context & Entity Tracking (Polymorphic)
            $table->string('entity_type')->nullable()->index(); // product, brand, service, project, event, page
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('entity_slug')->nullable()->index();

            // Context (CTA Position)
            $table->string('cta_position')->nullable()->index(); // header, footer, floating, sticky, hero_section

            // Technical info
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referrer_url')->nullable();
            $table->text('page_url')->nullable();

            // Contact Data / Target
            $table->string('target_value')->nullable(); // phone number, email, share link, etc

            $table->json('meta')->nullable(); // flexible extra data

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_click_events');
    }
};
