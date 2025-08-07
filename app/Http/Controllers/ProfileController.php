<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Tampilkan form profil user.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $request->user(),
        ]);
    }

    /**
     * Update data user (name, email, bio).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
           'bio' => $validated['bio'] ?? null,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'Profil berhasil diperbarui.');
    }

    /**
     * Upload foto profil baru.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => 'required|image|max:2048',
        ]);

        $user = $request->user();

        // Hapus foto lama jika ada
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Simpan foto baru
        $path = $request->file('photo')->store('profile-photos', 'public');

        $user->profile_photo_path = $path;
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'Foto profil diperbarui.');
    }

    /**
     * Upload background baru.
     */
    public function updateBackgroundPhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'background' => 'required|image|max:4096',
        ]);

        $user = $request->user();

        // Hapus background lama jika ada
        if ($user->background_photo_path) {
            Storage::disk('public')->delete($user->background_photo_path);
        }

        // Simpan background baru
        $path = $request->file('background')->store('background-photos', 'public');

        $user->background_photo_path = $path;
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'Background berhasil diperbarui.');
    }

    /**
     * Hapus akun user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
public function updatePublicProfile(Request $request): RedirectResponse
{
    $request->validate([
        'name' => 'required|string|max:255',
        'bio' => 'nullable|string|max:500',
    ]);

    $user = $request->user();
    $user->name = $request->name;
    $user->bio = $request->bio;
    $user->save();

    return Redirect::route('my.profile')->with('status', 'Profil publik berhasil diperbarui.');
}


}
