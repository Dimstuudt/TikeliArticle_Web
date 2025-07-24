<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Inertia\Inertia;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\Auth\VerifyEmailController;

// Models
use App\Models\User;

// Middleware
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\EnsureProfileComplete;

// Google OAuth
use Laravel\Socialite\Facades\Socialite;

// Redirect ke Google
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
});

// Callback dari Google
Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();

    $user = User::where('email', $googleUser->getEmail())->first();

    if (!$user) {
        $user = User::create([
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'password' => bcrypt(Str::random(24)),
        ]);
    } elseif (!$user->google_id) {
        $user->update([
            'google_id' => $googleUser->getId(),
        ]);
    }

    Auth::login($user);

    if (!$user->hasVerifiedEmail()) {
        $user->sendEmailVerificationNotification();
        return redirect()->route('verification.notice');
    }

    if (empty($user->username)) {
        return redirect()->route('complete-profile');
    }

    return redirect()->route('dashboard');
});

// Halaman root → redirect ke login
Route::get('/', fn () => redirect('/login'));

// Dashboard (harus login, verified, dan profil lengkap)
Route::get('/dashboard', fn () => Inertia::render('Dashboard'))
    ->middleware(['auth', 'verified', PreventBackHistory::class, EnsureProfileComplete::class])
    ->name('dashboard');

// Halaman untuk lengkapi profil
Route::get('/complete-profile', fn () => Inertia::render('Auth/CompleteProfile'))
    ->middleware(['auth'])
    ->name('complete-profile');

// Submit form lengkap profil
Route::post('/complete-profile', function (Request $request) {
    $request->validate([
        'username' => ['required', 'unique:users,username', 'regex:/^\S*$/u'],
        'password' => ['required', 'min:8'],
    ]);

    $user = Auth::user();
    $user->username = $request->username;
    $user->password = bcrypt($request->password);
    $user->save();

    return redirect()->route('dashboard');
})->middleware(['auth']);

// Profil
Route::middleware(['auth', PreventBackHistory::class, EnsureProfileComplete::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfilePhotoController::class, 'update'])->name('profile.photo.update');
});


// Notifikasi verifikasi email
Route::get('/email/verify', fn () => Inertia::render('Auth/VerifyEmail'))
    ->middleware('auth')
    ->name('verification.notice');

// Kirim ulang link verifikasi
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Tautan verifikasi dari email
Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

// Halaman khusus ketika sudah berhasil verifikasi
Route::get('/verified', fn () => Inertia::render('Auth/EmailVerified'))
    ->middleware('auth');

// Default auth routes dari Laravel Breeze
require __DIR__.'/auth.php';
