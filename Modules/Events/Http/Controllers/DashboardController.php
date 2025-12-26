<?php

namespace Modules\Events\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Modules\Events\Models\Event;
use Modules\Events\Models\EventRegistration;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('event')->user();

        // Calculate stats
        $totalEvents = EventRegistration::where('event_user_id', $user->id)->count();
        $activeEvents = EventRegistration::where('event_user_id', $user->id)
            ->whereHas('event', function($q) {
                $q->where('end_date', '>=', now())
                  ->orWhereNull('end_date');
            })->count();

        // Get recent registrations
        $recentEvents = EventRegistration::with('event')
            ->where('event_user_id', $user->id)
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($registration) {
                return [
                    'id' => $registration->event->id,
                    'title' => $registration->event->title,
                    'start_date' => $registration->event->start_date,
                    'status' => $registration->status,
                    'slug' => $registration->event->slug,
                    'thumbnail' => $registration->event->thumbnail ? '/storage/' . $registration->event->thumbnail : '/assets/img/event/event_thumb_1_1.jpg',
                ];
            });

        return Inertia::render('Events/Dashboard/Index', [
            'auth' => [
                'user' => $user,
            ],
            'stats' => [
                'total_events' => $totalEvents,
                'active_events' => $activeEvents,
            ],
            'recent_events' => $recentEvents,
        ]);
    }

    public function myEvents()
    {
        $user = Auth::guard('event')->user();

        $registrations = EventRegistration::with('event')
            ->where('event_user_id', $user->id)
            ->latest()
            ->paginate(9)
            ->through(function ($registration) {
                return [
                    'id' => $registration->event->id,
                    'title' => $registration->event->title,
                    'start_date' => $registration->event->start_date,
                    'location' => $registration->event->location,
                    'thumbnail' => $registration->event->thumbnail ? '/storage/' . $registration->event->thumbnail : null,
                    'status' => $registration->status,
                    'slug' => $registration->event->slug,
                    'meeting_link' => $registration->status === 'approved' ? $registration->event->meeting_link : null,
                ];
            });

        return Inertia::render('Events/Dashboard/MyEvents', [
             'auth' => [
                'user' => $user,
            ],
            'registrations' => $registrations,
        ]);
    }

    public function myEventDetail($slug)
    {
        $user = Auth::guard('event')->user();

        $event = Event::where('slug', $slug)->firstOrFail();

        // Formatted dates for frontend since model appends were removed
        $event->start_time_full = $event->start_date->format('d M Y H:i');
        $event->end_time_full = $event->end_date ? $event->end_date->format('d M Y H:i') : '-';

        $registration = EventRegistration::where('event_user_id', $user->id)
            ->where('event_id', $event->id)
            ->firstOrFail();

        $certificate_template = \Modules\Events\Models\EventCertificate::where('event_id', $event->id)->first();

        $user_certificate = \Modules\Events\Models\EventUserCertificate::where('event_id', $event->id)
            ->where('event_user_id', $user->id)
            ->first();

        return Inertia::render('Events/Dashboard/MyEventDetail', [
            'auth' => [
                'user' => $user,
            ],
            'event' => $event->load(['organizerInfo', 'documentations']),
            'registration' => $registration,
            'certificate_template' => $certificate_template,
            'user_certificate' => $user_certificate,
        ]);
    }

    public function eventRoom($slug)
    {
        $user = Auth::guard('event')->user();

        $event = Event::where('slug', $slug)->firstOrFail();

        // Verify registration
        $registration = EventRegistration::where('event_user_id', $user->id)
            ->where('event_id', $event->id)
            ->whereIn('status', ['approved', 'confirmed', 'paid']) // Allow confirmed/paid participants
            ->firstOrFail();

        return Inertia::render('Events/Dashboard/EventRoom', [
             'auth' => [
                'user' => $user,
            ],
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'meeting_link' => $event->meeting_link,
                'is_certificate_available' => $event->is_certificate_available,
                'has_certificate_template' => \Modules\Events\Models\EventCertificate::where('event_id', $event->id)->exists(),
                'schedule' => $event->schedule,
                'documentations' => $event->documentations,
            ],
            'registration' => $registration,
            'user_certificate' => \Modules\Events\Models\EventUserCertificate::where('event_id', $event->id)
                ->where('event_user_id', $user->id)
                ->first(),
        ]);
    }

    public function claimCertificate(Request $request, Event $event)
    {
        $user = Auth::guard('event')->user();

        // 1. Verify Event allows certificates
        if (!$event->is_certificate_available) {
             return back()->with('error', 'Certificate not available for this event.');
        }

        // 2. Verify Registration (must be approved/attended)
        $registration = EventRegistration::where('event_user_id', $user->id)
            ->where('event_id', $event->id)
            ->whereIn('status', ['approved', 'confirmed', 'paid'])
            ->firstOrFail();

        // 3. Check if already claimed
        $existing = \Modules\Events\Models\EventUserCertificate::where('event_id', $event->id)
            ->where('event_user_id', $user->id)
            ->first();

        if ($existing) {
             return back()->with('success', 'Certificate already claimed.');
        }

        // 4. Create Certificate Record
        $template = \Modules\Events\Models\EventCertificate::where('event_id', $event->id)->firstOrFail();

        $generator = new \Modules\Events\Services\CertificateGenerator();
        $result = $generator->generate($user, $event, $template);

        $cert = \Modules\Events\Models\EventUserCertificate::create([
            'event_id' => $event->id,
            'event_user_id' => $user->id,
            'certificate_code' => $result['code'],
            'file_path' => $result['path'],
        ]);

        return back()->with('success', 'Certificate claimed successfully!');
    }
    public function payments()
    {
        $user = Auth::guard('event')->user();

        $registrations = EventRegistration::with('event')
            ->where('event_user_id', $user->id)
            ->latest()
            ->paginate(10)
            ->through(function ($registration) {
                return [
                    'id' => $registration->id,
                    'invoice_id' => 'INV-' . str_pad($registration->id, 6, '0', STR_PAD_LEFT),
                    'event_title' => $registration->event->title,
                    'event_date' => $registration->event->start_date->format('d M Y'),
                    'amount' => $registration->event->price == 0 ? 'Free' : $registration->event->price,
                    'event_organizer_phone' => $registration->event->organizerInfo->phone,
                    'status' => $registration->status,
                    'date' => $registration->created_at->format('d M Y H:i'),
                ];
            });

        return Inertia::render('Events/Dashboard/Payment', [
            'auth' => ['user' => $user],
            'payments' => $registrations,
        ]);
    }

    public function certificates()
    {
        $user = Auth::guard('event')->user();

        $certificates = \Modules\Events\Models\EventUserCertificate::with('event')
            ->where('event_user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($cert) {
                return [
                    'id' => $cert->id,
                    'event_title' => $cert->event->title,
                    'event_date' => $cert->event->start_date->format('d M Y'),
                    'file_path' => '/storage/' . $cert->file_path,
                    'issue_date' => $cert->created_at->format('d M Y'),
                    'event_thumbnail' => $cert->event->thumbnail ? '/storage/' . $cert->event->thumbnail : '/assets/img/event/event_thumb_1_1.jpg',
                ];
            });

        return Inertia::render('Events/Dashboard/Certificate', [
            'auth' => ['user' => $user],
            'certificates' => $certificates,
        ]);
    }

    public function profile()
    {
        $user = Auth::guard('event')->user();

        return Inertia::render('Events/Dashboard/Profile', [
            'auth' => ['user' => $user],
            'status' => session('status'),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('event')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:event_users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|max:1024', // 1MB Max
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];

        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        if ($request->hasFile('avatar')) {
             // Handle avatar upload - simple storage for now
             $path = $request->file('avatar')->store('avatars', 'public');
             $user->avatar = '/storage/' . $path;
        }

        $user->save();

        return back()->with('status', 'profile-updated');
    }
}
