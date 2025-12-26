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
        $data = [
            'participant_name' => $user->name,
            'event_title' => $event->title,
            'date' => now()->format('d F Y'),
            'certificate_code' => 'CERT-' . strtoupper(Str::random(10)),
            'qr_code' => '', // TODO: Generate QR
        ];

        // 2. Parse Layout
        $layout = $template->content_layout ?? [];
        $background = $template->certificate_background;

        // 3. Render HTML (Blade View)
        // We render a simple HTML that mimics the React Canvas using absolute positioning
        $pdf = Pdf::loadView('events::certificate.pdf_template', [
            'layout' => $layout,
            'background' => $background,
            'data' => $data,
        ])->setPaper('a4', 'landscape');

        // 4. Save to Storage
        $filename = 'certificates/' . $data['certificate_code'] . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        return [
            'path' => $filename,
            'code' => $data['certificate_code']
        ];
    }
}
