<?php

namespace Modules\Events\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Events\Models\Event;
use Modules\Events\Models\EventUser;
use Modules\Events\Models\EventRegistration;

class EventRegistrationController extends Controller
{
    public function join(Request $request, Event $event)
    {
        $user = Auth::guard('event')->user();

        if ($user) {
            // User is already logged in
        } else {
            // Guest or New User
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'phone' => 'nullable|string|max:20',
                'password' => 'nullable|string|min:8',
            ]);

            $user = EventUser::where('email', $validated['email'])->first();

            if ($user) {
                // If user exists, verify password if provided
                if ($request->filled('password')) {
                    if (!Hash::check($request->password, $user->password)) {
                        return back()->withErrors(['password' => 'Password mismatch for this email.']);
                    }
                    Auth::guard('event')->login($user);
                } else {
                    return back()->withErrors(['email' => 'Account exists. Please log in or provide password.']);
                }
            } else {
                // New user
                if (!$request->filled('password')) {
                    return back()->withErrors(['password' => 'Password is required for new registration.']);
                }

                $user = EventUser::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'password' => Hash::make($request->password),
                ]);
                Auth::guard('event')->login($user);
            }
        }

        // Determine status and amount
        // Assuming price is stored as string like "Free" or "50000"
        $price = $event->price;
        $isFree = $price === 'Free' || empty($price) || $price == 0;
        $amount = $isFree ? 0 : (float) preg_replace('/[^0-9.]/', '', $price);

        $registration = EventRegistration::firstOrCreate(
            [
                'event_id' => $event->id,
                'event_user_id' => $user->id,
            ],
            [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'amount' => $amount,
                'status' => $isFree ? 'confirmed' : 'pending',
            ]
        );

        return redirect()->route('events.my-events')->with('success', 'Successfully registered for the event!');
    }
}
