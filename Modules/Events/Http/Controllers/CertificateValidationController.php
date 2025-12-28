<?php

namespace Modules\Events\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Events\Models\EventUserCertificate;

use Illuminate\Support\Facades\Storage;

class CertificateValidationController extends Controller
{
    public function verify($code)
    {
        $certificate = EventUserCertificate::with(['event', 'eventUser'])
            ->where('certificate_code', $code)
            ->first();

        // Note: Relation names might vary. Let's rely on standard relations.
        // EventUserCertificate belongsTo Event, belongsTo EventUser.

        $status = $certificate ? 'valid' : 'invalid';

        return Inertia::render('Events/Certificate/Verify', [
            'status' => $status,
            'certificate' => $certificate ? [
                'code' => $certificate->certificate_code,
                'participant' => $certificate->eventUser->name ?? 'Unknown',
                'event_title' => $certificate->event->title ?? 'Unknown',
                'issued_at' => $certificate->created_at->format('d F Y'),
                'file_url' => '/storage/' . $certificate->file_path,
            ] : null,
            'code' => $code
        ]);
    }

    public function download($code)
    {
        $certificate = EventUserCertificate::where('certificate_code', $code)->firstOrFail();

        $path = $certificate->file_path;

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'Certificate file not found.');
        }

        return Storage::disk('public')->download(
            $path,
            'Certificate-' . $code . '.pdf'
        );
    }
}
