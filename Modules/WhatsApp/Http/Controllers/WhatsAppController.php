<?php

namespace Modules\WhatsApp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class WhatsAppController extends Controller
{
    public function redirect(Request $request)
    {
        // Get WhatsApp Number (Priority: Parameter > Settings)
        $whatsappNumber = $request->input('phone');
        
        if (!$whatsappNumber) {
            $settings = DB::table('settings')->where('key', 'whatsapp_number')->first();
            $whatsappNumber = $settings ? $settings->value : null;
        }

        if (!$whatsappNumber) {
            return redirect('/')->with('error', 'WhatsApp number not configured.');
        }

        $text = $request->input('text', 'Halo ACTiV, saya ingin bertanya mengenai layanan Anda.');

        // Clean WhatsApp number
        $number = preg_replace('/[^0-9]/', '', $whatsappNumber);
        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        }

        // WhatsApp redirect URL
        $whatsappUrl = "https://wa.me/{$number}?text=" . urlencode($text);

        // Enterprise Logic: Session ID
        $sessionId = session()->get('analytics_session_id', (string) \Illuminate\Support\Str::uuid());
        session()->put('analytics_session_id', $sessionId);

        // Detect Bot and User Agent
        $userAgent = $request->userAgent();
        $isBot = preg_match('/bot|crawl|slurp|spider|mediapartners/i', $userAgent);

        // Detect Device Type
        $device = 'desktop';
        if (preg_match('/mobile|android|iphone|ipad|phone/i', $userAgent)) {
            $device = 'mobile';
        }

        // Session-Based Aggregation (1-hour window) - Only if context matches
        $existingEvent = \Modules\Analytics\Models\AnalyticsClickEvent::where('ip_address', $request->ip())
            ->where('event_type', 'whatsapp')
            ->where('target_value', $number)
            ->where('cta_position', $request->input('cta_position'))
            ->where('utm_source', $request->input('utm_source'))
            ->where('utm_medium', $request->input('utm_medium'))
            ->where('utm_campaign', $request->input('utm_campaign'))
            ->where('entity_id', $request->input('entity_id'))
            ->where('page_url', $request->input('page_route'))
            ->where('created_at', '>=', now()->subHour())
            ->first();

        if ($existingEvent && !$isBot) {
            $existingEvent->increment('click_count');
            
            $newReferrer = $request->header('referer') ?: $request->input('page_route');
            $updateData = [];

            // Heal Referrer if current is bad and new is good
            $currentReferrer = $existingEvent->referrer_url;
            $currentIsBad = empty($currentReferrer) || str_contains($currentReferrer, '/activioncms');
            $newIsGood = !empty($newReferrer) && !str_contains($newReferrer, '/activioncms');

            if ($currentIsBad && $newIsGood) {
                $updateData['referrer_url'] = $newReferrer;
            }

            // Optionally update event_label if it was null
            if (empty($existingEvent->event_label) && $request->input('cta_label')) {
                $updateData['event_label'] = $request->input('cta_label');
            }

            if (!empty($updateData)) {
                $existingEvent->update($updateData);
            }
        } else {
            $event = \Modules\Analytics\Models\AnalyticsClickEvent::create([
                'session_id' => $sessionId,
                'event_type' => 'whatsapp',
                'event_label' => $request->input('cta_label'),
                'utm_source' => $request->input('utm_source'),
                'utm_medium' => $request->input('utm_medium'),
                'utm_campaign' => $request->input('utm_campaign'),
                'utm_term' => $request->input('utm_term'),
                'utm_content' => $request->input('utm_content'),
                'entity_type' => $request->input('entity_type'),
                'entity_id' => $request->input('entity_id'),
                'entity_slug' => $request->input('entity_slug'),
                'cta_position' => $request->input('cta_position'),
                'ip_address' => $request->ip(),
                'user_agent' => $userAgent,
                'device' => $device,
                'referrer_url' => $request->header('referer') ?: $request->input('page_route'),
                'page_url' => $request->input('page_route'),
                'target_value' => $number,
                'is_bot' => $isBot,
                'meta' => [
                    'message' => $text,
                ],
            ]);

            // Dispatch Geo-Enrichment Job
            if (!$isBot) {
                \Modules\Analytics\Jobs\ResolveGeoLocation::dispatch($event);
            }
        }

        return redirect($whatsappUrl);
    }
}
