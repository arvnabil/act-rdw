<?php

namespace Modules\Events\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Events\Models\EventUser;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Events/Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('event')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('events.dashboard')->with('success', 'Login Berhasil!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return Inertia::render('Events/Auth/Register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:event_users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = EventUser::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('event')->login($user);

        return redirect()->route('events.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('event')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('events.login');
    }
}
