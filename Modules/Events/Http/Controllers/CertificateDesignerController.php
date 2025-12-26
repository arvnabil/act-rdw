<?php

namespace Modules\Events\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Events\Models\EventCertificate;

class CertificateDesignerController extends Controller
{
    public function edit($id)
    {
        $certificate = EventCertificate::with('event')->findOrFail($id);

        return Inertia::render('Filament/Events/CertificateDesigner', [
            'certificate' => $certificate,
            'event' => $certificate->event,
        ]);
    }

    public function update(Request $request, $id)
    {
        $certificate = EventCertificate::findOrFail($id);

        $validated = $request->validate([
            'content_layout' => 'required|array',
            'certificate_background' => 'nullable|string', // Just in case they update bg from here
        ]);

        $certificate->update([
            'content_layout' => $validated['content_layout'],
        ]);

        return back()->with('success', 'Design saved successfully!');
    }
}
