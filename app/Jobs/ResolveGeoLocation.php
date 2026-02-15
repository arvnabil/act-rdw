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
            $response = \Illuminate\Support\Facades\Http::get("http://ip-api.com/json/{$this->event->ip_address}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'success') {
                    $this->event->update([
                        'city'    => $data['city'] ?? null,
                        'region'  => $data['regionName'] ?? null,
                        'country' => $data['country'] ?? null,
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("GeoIP Error: " . $e->getMessage());
        }
    }
}
