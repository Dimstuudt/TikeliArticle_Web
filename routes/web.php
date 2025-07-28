<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\Auth\VerifyEmailController;

// Models
use App\Models\User;

// Middleware
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\EnsureProfileComplete;
use App\Http\Middleware\RoleMiddleware;

// === Google OAuth Routes ===
Route::get('/auth/google', fn () => Socialite::driver('google')->redirect());

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();

    $user = User::firstOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            'google_id' => $googleUser->getId(),
            'password' => bcrypt(Str::random(24)),
        ]
    );

    // Update google_id jika belum ada
    if (!$user->google_id) {
        $user->update(['google_id' => $googleUser->getId()]);
    }

    Auth::login($user);

    // Email belum terverifikasi
    if (!$user->hasVerifiedEmail()) {
        $user->sendEmailVerificationNotification();
        return redirect()->route('verification.notice');
    }

    // User belum isi username & password
    if (empty($user->username)) {
        return redirect()->route('complete-profile');
    }

    // Redirect sesuai role
    return redirect()->route(
        $user->role === 'admin' ? 'admin.dashboard' : 'operator.dashboard'
    );
});

// === Redirect root ke login ===
Route::get('/', fn () => redirect('/login'));

// === Lengkapi profil (untuk user Google) ===
Route::middleware(['auth'])->group(function () {
    Route::get('/complete-profile', fn () => Inertia::render('Auth/CompleteProfile'))
        ->name('complete-profile');

    Route::post('/complete-profile', function (Request $request) {
        $request->validate([
            'username' => ['required', 'unique:users,username', 'regex:/^\S*$/u'],
            'password' => ['required', 'min:8'],
        ]);

        $user = Auth::user();
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->save();

        return redirect()->route(
            $user->role === 'admin' ? 'admin.dashboard' : 'operator.dashboard'
        );
    });
});

// === Dashboard Redirect berdasarkan Role ===
Route::get('/dashboard', function () {
    $user = auth()->user();
    return redirect()->route(
        $user->role === 'admin' ? 'admin.dashboard' : 'operator.dashboard'
    );
})->middleware(['auth', 'verified', PreventBackHistory::class, EnsureProfileComplete::class])
  ->name('dashboard');

// === Dashboard Admin ===
Route::middleware([
    'auth',
    'verified',
    PreventBackHistory::class,
    EnsureProfileComplete::class,
    RoleMiddleware::class . ':admin'
])->group(function () {
    Route::get('/admin', fn () => Inertia::render('Admin/Dashboard'))
        ->name('admin.dashboard');
});

// === Dashboard Operator ===
Route::middleware([
    'auth',
    'verified',
    PreventBackHistory::class,
    EnsureProfileComplete::class,
    RoleMiddleware::class . ':operator'
])->group(function () {
    Route::get('/operator', fn () => Inertia::render('Operator/Dashboard'))
        ->name('operator.dashboard');
});

// === Profile & Foto Profil (bisa diakses semua role) ===
Route::middleware(['auth', PreventBackHistory::class, EnsureProfileComplete::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfilePhotoController::class, 'update'])->name('profile.photo.update');
});

// === Verifikasi Email ===
Route::get('/email/verify', fn () => Inertia::render('Auth/VerifyEmail'))
    ->middleware('auth')->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::get('/verified', fn () => Inertia::render('Auth/EmailVerified'))->middleware('auth');

// === Auth Routes dari Breeze ===
require __DIR__.'/auth.php';
