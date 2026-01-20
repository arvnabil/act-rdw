<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AnalyzeFormSecurityJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        // 1. Get all forms with recent activity (last 24h)
        $forms = \DB::table('form_analytics')
            ->select('form_key')
            ->where('created_at', '>=', now()->subDay())
            ->distinct()
            ->pluck('form_key');

        foreach ($forms as $formKey) {
            // Stats
            $views = \DB::table('form_analytics')
                ->where('form_key', $formKey)
                ->where('created_at', '>=', now()->subDay())
                ->count();

            $submissions = \DB::table('form_security_logs')
                ->where('form_key', $formKey)
                ->where('created_at', '>=', now()->subDay())
                ->count();

            $blocks = \DB::table('form_security_logs')
                ->where('form_key', $formKey)
                ->where('decision', 'block')
                ->where('created_at', '>=', now()->subDay())
                ->count();

            $spamRate = $submissions > 0 ? ($blocks / $submissions) * 100 : 0;
            $conversionRate = $views > 0 ? ($submissions / $views) * 100 : 0;

            // Logic: Auto-Disable CAPTCHA if safe
            // If Spam Rate < 1% AND Conversion Rate < 10% (Low conversion)
            // We disable CAPTCHA to boost conversion
            $shouldEnableCaptcha = true;

            if ($spamRate < 1 && $conversionRate < 10) {
                 $shouldEnableCaptcha = false;
            }

            // Force Enable if spam is high
            if ($spamRate > 5) {
                $shouldEnableCaptcha = true;
            }

            // Update or Create Settings
            // Check if "auto_managed" is true (if record exists)
            $settings = \DB::table('form_security_settings')->where('form_key', $formKey)->first();

            if (!$settings || $settings->auto_managed) {
                \DB::table('form_security_settings')->updateOrInsert(
                    ['form_key' => $formKey],
                    [
                        'captcha_enabled' => $shouldEnableCaptcha,
                        'auto_managed' => true,
                        'last_decision_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
