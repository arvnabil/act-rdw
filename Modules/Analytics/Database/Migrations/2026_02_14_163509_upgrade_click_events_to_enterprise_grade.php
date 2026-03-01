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
        Schema::table('analytics_click_events', function (Blueprint $table) {
            // Enterprise Session Tracking
            $table->uuid('session_id')->nullable()->after('id')->index();
            $table->unsignedInteger('click_count')->default(1)->after('target_value');
            
            // Bot Detection
            $table->boolean('is_bot')->default(false)->after('click_count');
            
            // Conversion Tracking
            $table->boolean('is_converted')->default(false)->after('is_bot')->index();
            $table->unsignedBigInteger('lead_id')->nullable()->after('is_converted');
            $table->decimal('deal_value', 15, 2)->nullable()->after('lead_id');

            // Geo-Resolution Placeholders (For Async Job)
            $table->string('city')->nullable()->after('ip_address');
            $table->string('region')->nullable()->after('city');
            $table->string('country')->nullable()->after('region');

            // Composite Indexes for High Performance
            $table->index(['event_type', 'created_at'], 'idx_type_created');
            $table->index(['utm_source', 'created_at'], 'idx_utm_created');
            $table->index(['entity_type', 'entity_id'], 'idx_entity_polymorphic');
            $table->index(['cta_position', 'created_at'], 'idx_cta_created');
            $table->index(['session_id', 'created_at'], 'idx_session_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('analytics_click_events', function (Blueprint $table) {
            // Use dropIfExists/ignore errors if needed, but standard drop is fine if we are sure it exists
            // To be safe against partial migrations:
            try {
                $table->dropIndex('idx_type_created');
                $table->dropIndex('idx_utm_created');
                $table->dropIndex('idx_entity_polymorphic');
                $table->dropIndex('idx_cta_created');
                $table->dropIndex('idx_session_created');

                $table->dropColumn([
                    'session_id',
                    'click_count',
                    'is_bot',
                    'is_converted',
                    'lead_id',
                    'deal_value',
                    'city',
                    'region',
                    'country'
                ]);
            } catch (\Exception $e) {
                // Silently fail if columns/indexes don't exist
            }
        });
    }
};
