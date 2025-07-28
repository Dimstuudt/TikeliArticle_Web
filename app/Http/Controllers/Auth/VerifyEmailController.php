<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Handle the email verification request.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->redirectAfterVerification($user);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->redirectAfterVerification($user);
    }

    /**
     * Redirect user after email verification based on login method and role.
     */
    protected function redirectAfterVerification($user): RedirectResponse
    {
        if ($user->google_id) {
            // User login via Google, tetap dalam sesi dan redirect sesuai role
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'operator' => redirect()->route('operator.dashboard'),
                default => redirect()->route('dashboard'),
            };
        }

        // User login manual, logout dulu lalu redirect ke login dengan pesan sukses
        auth()->logout();
        return redirect()->route('login')->with('status', 'Email berhasil diverifikasi. Silakan login.');
    }
}
