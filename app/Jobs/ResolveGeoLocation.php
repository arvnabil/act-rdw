<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ResolveGeoLocation implements ShouldQueue
{
    use Queueable;

    public $event;

    /**
     * Create a new job instance.
     */
    public function __construct(\Modules\Core\Models\AnalyticsClickEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->event->ip_address) {
            return;
        }

        try {
            $token = \Illuminate\Support\Facades\DB::table('settings')->where('key', 'ipinfo_token')->value('value');
            $url = "https://ipinfo.io/{$this->event->ip_address}/json";
            
            if ($token) {
                $url .= "?token={$token}";
            }

            $response = \Illuminate\Support\Facades\Http::get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                
                $this->event->update([
                    'city'    => $data['city'] ?? null,
                    'region'  => $data['region'] ?? null,
                    'country' => $data['country'] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("GeoIP Error: " . $e->getMessage());
        }
    }
}
