<?php

namespace Modules\Events\Services;

use Modules\Events\Models\EventCertificate;
use Modules\Events\Models\EventUser;
use Modules\Events\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CertificateGenerator
{
    public function generate(EventUser $user, Event $event, EventCertificate $template)
    {
        // 1. Prepare Data
        $code = 'ACT-CERT-' . strtoupper(Str::random(10));

        // Generate QR Code acting as validation link
        // Needs "simplesoftwareio/simple-qrcode" installed
        $validationUrl = route('events.certificates.verify', $code);

        // Using SVG format to avoid needing Imagick/GD extensions
        $qrRaw = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->generate($validationUrl);
        $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrRaw);

        $data = [
            'participant_name' => $user->name,
            'event_title' => $event->title,
            'date' => now()->format('d F Y'), // Default format
            'date_obj' => now(), // For custom formatting
            'certificate_code' => $code,
            'qr_code' => $qrBase64,
        ];

        // 2. Parse & Process Layout
        $layout = $template->content_layout ?? [];
        $background = $template->certificate_background;

        // Process Interpolation
        $processedLayout = array_map(function ($element) use ($data) {
            if (in_array($element['type'], ['text', 'variable']) && isset($element['text'])) {
                // Regex to find {{ variable }} patterns
                $element['text'] = preg_replace_callback('/{{\s*([a-zA-Z0-9_]+)\s*}}/', function ($matches) use ($data) {
                    $key = strtolower($matches[1]); // Normalize to lowercase keys
                    // Handle "date" specifically if needed, or just look up in data
                    return $data[$key] ?? $matches[0]; // Return value or original placeholder if not found
                }, $element['text']);
            }
            return $element;
        }, $layout);

        // 3. Render HTML (Blade View)
        $pdf = Pdf::loadView('events::certificate.pdf_template', [
            'layout' => $processedLayout,
            'background' => $background,
            'data' => $data,
        ])->setPaper('a4', 'landscape');

        // 4. Save to Storage
        $slug = $event->slug ?? 'default';
        $filename = "events/{$slug}/certificates/" . $data['certificate_code'] . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        return [
            'path' => $filename,
            'code' => $data['certificate_code']
        ];
    }
}
