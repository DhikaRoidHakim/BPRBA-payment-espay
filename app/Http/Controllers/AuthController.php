<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Spatie\Activitylog\Models\Activity;


class AuthController extends Controller
{
    //
    public function showFromLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard')->with('success', 'Anda sudah login!');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $cretendials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|max:26'
        ]);


        if (Auth::attempt($cretendials, $request->filled('remember'))) {
            activity('login')
                ->performedOn(Auth::user())
                ->causedBy(Auth::user())
                ->withProperties(['email' => $cretendials['email']])
                ->log('Login Berhasil!');
            return redirect()->intended(route('dashboard'))->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        activity('logout')
            ->performedOn(Auth::user())
            ->causedBy(Auth::user())
            ->withProperties(['email' => Auth::user()->email])
            ->log('Logout Berhasil!');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda berhasil logout!');
    }
}
