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
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // 1. JIKA ADMIN -> Gunakan view khusus Admin (yang sudah ada)
        if ($user->role === 'admin') {
            return view('admin.profile', [
                'user' => $user,
            ]);
        }

        // 2. JIKA TEACHER -> Gunakan view khusus Teacher (yang baru dibuat)
        if ($user->is_teacher) {
            return view('teacher.profile', [
                'user' => $user,
            ]);
        }

        // 3. DEFAULT -> View standar Breeze
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // UPDATE ROLE LOGIC (HANYA JIKA ADMIN YANG REQUEST)
        // Ini memastikan guru tidak bisa mengubah 'is_teacher' mereka sendiri 
        // meskipun mereka memanipulasi HTML form.
        if ($user->role === 'admin') {
            $user->is_teacher = $request->boolean('is_teacher');
        }

        $user->save();

        // Redirect selalu ke 'profile.edit', controller edit() akan mengurus view-nya.
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