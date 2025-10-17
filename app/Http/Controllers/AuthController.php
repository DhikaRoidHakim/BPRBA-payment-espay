<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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
            return redirect()->intended(route('dashboard'))->with('success', 'Login berhasil!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda berhasil logout!');
    }
}
