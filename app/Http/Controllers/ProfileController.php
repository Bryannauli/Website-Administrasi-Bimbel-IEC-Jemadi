<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // Display the admin user's profile form.
    public function editAdmin(Request $request): View
    {
        return view('admin.profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Update Data Standar (Name, Email)
        $user->fill($request->validated());

        // 2. Reset Verifikasi Email jika berubah
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 3. UPDATE STATUS GURU (LOGIKA DIPERBAIKI)
        // Hanya update 'is_teacher' jika user yang login adalah ADMIN.
        // Guru biasa tidak boleh mengubah status ini sendiri, 
        // dan form guru tidak punya input ini (menghindari set to false otomatis).
        if ($user->role === 'admin') {
            $user->is_teacher = $request->boolean('is_teacher');
        }

        // 4. Simpan
        $user->save();

        // 5. Redirect sesuai Role
        if ($user->role === 'admin') {
            return Redirect::route('admin.profile')->with('status', 'profile-updated');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
