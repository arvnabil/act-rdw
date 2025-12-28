<?php

namespace Modules\Events\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Events\Models\Event;
use Modules\Events\Models\EventRegistration;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function verify($slug, $code)
    {
        // 1. Check Authentication (Event User)
        if (!\Illuminate\Support\Facades\Auth::guard('event')->check()) {
            return redirect()->route('events.login')->with('error', 'Please login to verify tickets.');
        }

        $user = \Illuminate\Support\Facades\Auth::guard('event')->user();
        $event = Event::where('slug', $slug)->firstOrFail();

        // 2. Check Permission (Is this user an Organizer for this event?)
        // Assuming user->organizers() relationship exists and Event has organizer_id
        if (!$event->organizer_id || !$user->organizers()->where('organizers.id', $event->organizer_id)->exists()) {
             return Inertia::render('Events/TicketVerify', [
                 'status' => 'error',
                 'message' => 'You do not have the permission to see Event Attenders.',
                 'event' => $event,
             ]);
        }

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('ticket_code', $code)
            ->with(['user', 'event'])
            ->first();

        if (!$registration) {
            return Inertia::render('Events/TicketVerify', [
                'status' => 'error',
                'message' => 'Ticket not found or invalid.',
            ]);
        }

        if ($registration->status === 'Pending') {
            return Inertia::render('Events/TicketVerify', [
                'status' => 'warning',
                'message' => 'Sorry, your ticket is not yet confirmed.',
                'event' => $event,
                'registration' => $registration,
                'user' => $registration->user,
            ]);
        }

        // Update status to Registered if it's not already Certified
        if ($registration->status !== 'Certified') {
            $registration->update(['status' => 'Registered']);
        }

        return Inertia::render('Events/TicketVerify', [
            'status' => 'success',
            'event' => $event,
            'registration' => $registration,
            'user' => $registration->user,
        ]);
    }
}
