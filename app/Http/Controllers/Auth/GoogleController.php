<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal login dengan Google.',
            ]);
        }

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName() ?? 'Pengguna Google',
                'username' => 'google_' . Str::random(10),
                'email_verified_at' => now(),
                'password' => bcrypt(Str::random(16)),
                'role' => 'operator',
                'is_active' => true,
            ]
        );

        // Nonaktif? Gagal login
        if (!$user->is_active) {
            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan.',
            ]);
        }

        // Simpan google_id jika belum ada
        if (!$user->google_id) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        Auth::login($user, true);

        // Verifikasi email jika belum
        if (!$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return redirect()->route('verification.notice');
        }

        // Kalau username dummy, paksa lengkapi profil
        if (str_starts_with($user->username, 'google_')) {
            return redirect()->route('complete-profile');
        }

        // Redirect sesuai role
        return redirect()->route(
            $user->role === 'admin' ? 'admin.dashboard' : 'operator.dashboard'
        );
    }
}
