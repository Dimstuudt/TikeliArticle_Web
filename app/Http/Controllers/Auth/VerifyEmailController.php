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
     * Determine redirect destination after email verification.
     */
    protected function redirectAfterVerification($user): RedirectResponse
    {
        if ($user->google_id) {
            // OAuth user (Google login) tetap dalam sesi dan langsung ke dashboard
            return redirect()->route('dashboard');
        }

        // User biasa (registrasi manual) → logout lalu ke login
        auth()->logout();
        return redirect()->route('login')->with('status', 'Email berhasil diverifikasi. Silakan login.');
    }
}
