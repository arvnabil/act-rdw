<?php

namespace Modules\Events\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Events\Models\Event;
use Modules\Events\Models\EventUser;
use Modules\Events\Models\EventRegistration;
use Illuminate\Support\Str;

class EventRegistrationController extends Controller
{
    public function join(Request $request, Event $event)
    {
        $user = Auth::guard('event')->user();

        if ($user) {
            // User is already logged in
        } else {
            // Guest or New User
            // Dynamic validation: Name is only required if user does not exist
            $email = $request->input('email');
            $userExists = EventUser::where('email', $email)->exists();

            $rules = [
                'email' => 'required|string|email|max:255',
                'password' => 'nullable|string|min:8',
                'phone' => 'nullable|string|max:20',
            ];

            if (!$userExists) {
                $rules['name'] = 'required|string|max:255';
            }

            $validated = $request->validate($rules);

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
        $price = (float) $event->price;
        $isFree = $price == 0;
        $amount = $price;

        $invoiceNumber = 'ACT-INV' . str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
        $ticketCode = strtoupper(Str::random(5)); // Simple 5 char code e.g. VDR50

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
                'status' => $isFree ? 'Joined' : 'Pending',
                'invoice_number' => $invoiceNumber,
                'ticket_code' => $ticketCode,
            ]
        );

        $message = $registration->wasRecentlyCreated 
            ? 'Successfully registered for the event!' 
            : 'You have already joined this event!';

        return redirect()->route('events.my-events')->with('success', $message);
    }
}
