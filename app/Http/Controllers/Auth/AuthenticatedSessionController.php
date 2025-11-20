<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
public function store(Request $request)
{
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    if (!Auth::attempt([
        'username' => $request->username,
        'password' => $request->password,
        'is_active' => true, // Pastikan hanya login jika akun aktif
    ], $request->boolean('remember'))) {

        return back()->withErrors([
            'username' => 'Invalid credentials or your account is inactive.',
        ]);
    }

    $request->session()->regenerate();

    $user = Auth::user();

    // Redirect berdasarkan role (prioritaskan admin terlebih dahulu)
    if ($user->role == 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user->is_teacher) {
        return redirect()->route('teacher.dashboard');
    }

    // Tidak ada role lain
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login')->with('error', 'You do not have access to this application. Please contact the administrator.');
}



    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
