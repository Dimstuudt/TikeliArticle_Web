<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ArticleLikeController;
use App\Http\Controllers\Guest\UserProfileController;
use App\Http\Controllers\CommentController;

// Middleware
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\EnsureProfileComplete;
use App\Http\Middleware\RoleMiddleware;

// Models
use App\Models\User;
use App\Models\Article;

// Redirect root ke login
Route::get('/', fn () => redirect('/login'));


// === Google OAuth ===
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('login.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);



// === Lengkapi Profil ===
Route::middleware('auth')->group(function () {
    Route::get('/complete-profile', fn () => Inertia::render('Auth/CompleteProfile'))
        ->name('complete-profile');

    Route::post('/complete-profile', function (Request $request) {
        $request->validate([
            'username' => ['required', 'unique:users,username', 'regex:/^\S*$/u'],
            'password' => ['required', 'min:8'],
        ]);

        $user = Auth::user();
        $user->update([
            'username' => $request->username,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route(
            $user->role === 'admin' ? 'admin.dashboard' : 'operator.dashboard'
        );
    });
});

// === Dashboard Redirect sesuai role ===
Route::middleware([
    'auth',
    'verified',
    PreventBackHistory::class,
    EnsureProfileComplete::class,
])->get('/dashboard', function () {
    $user = auth()->user();
    return redirect()->route(
        $user->role === 'admin' ? 'admin.dashboard' : 'operator.dashboard'
    );
})->name('dashboard');

// === ADMIN Routes ===
Route::middleware([
    'auth',
    'verified',
    PreventBackHistory::class,
    EnsureProfileComplete::class,
    RoleMiddleware::class . ':admin',
])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'adminIndex'])->name('dashboard');


    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

// Toggle aktif/nonaktif user
Route::patch('/admin/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
    ->name('admin.users.toggleActive');

// Toggle trusted writer
Route::patch('/admin/users/{user}/toggle-trusted', [UserController::class, 'toggleTrusted'])
    ->name('admin.users.toggleTrusted');

    // Artikel admin
    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::put('/articles/{article}/approve', [ArticleController::class, 'approve'])->name('articles.approve');
    Route::put('/articles/{article}/reject', [ArticleController::class, 'reject'])->name('articles.reject');
    Route::get('/admin/articles/{article}', [ArticleController::class, 'show'])->name('admin.articles.show');
    Route::get('/articles/approved', [ArticleController::class, 'approved'])->name('admin.articles.approved');
Route::delete('/articles/{id}', [ArticleController::class, 'destroy'])->name('admin.articles.destroy');


//admin approved
Route::get('/articles/approved', [\App\Http\Controllers\Admin\ApprovedArticleController::class, 'index'])
            ->name('approved-articles.index');

     Route::delete('/articles/approved/{id}', [\App\Http\Controllers\Admin\ApprovedArticleController::class, 'destroy'])
    ->name('approved-articles.destroy');



});


Route::middleware([
    'auth',
    'verified',
    PreventBackHistory::class,
    EnsureProfileComplete::class,
    RoleMiddleware::class . ':operator',
])->prefix('operator')->name('operator.')->group(function () {

    // Dashboard operator (pakai controller)
    Route::get('/', [DashboardController::class, 'operatorIndex'])->name('dashboard');

    // Artikel operator
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create'); // URL lowercase
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/mine', [ArticleController::class, 'mine'])->name('articles.mine');
    Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::post('/articles/{article}/edit-save', [ArticleController::class, 'saveEdit'])->name('articles.saveEdit');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');
});





// === Profile Routes ===
Route::middleware([
    'auth',
    PreventBackHistory::class,
    EnsureProfileComplete::class,
])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfilePhotoController::class, 'update'])->name('profile.photo.update');
});

// === Email Verification Routes ===
Route::get('/email/verify', fn () => Inertia::render('Auth/VerifyEmail'))
    ->middleware('auth')->name('verification.notice');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::get('/verified', fn () => Inertia::render('Auth/EmailVerified'))
    ->middleware('auth');

// === Guest Routes ===

Route::get('/welcome', [ArticleController::class, 'landing'])->name('guest.welcome');

//guest see


Route::get('/articles/{id}', [ArticleController::class, 'guestShow'])
    ->name('guest.articles.show');

Route::get('/users/{user}', [UserProfileController::class, 'show'])
    ->name('guest.profile');


//like


Route::post('/articles/{article}/like', [ArticleLikeController::class, 'toggle'])
    ->middleware('auth')
    ->name('articles.like');


// MyProfile - edit profil publik (nama, bio, foto profil, background)
Route::middleware(['auth'])->group(function () {
    // Tampilkan halaman profil publik
    Route::get('/my-profile', function () {
        return Inertia::render('Profile/MyProfile');
    })->name('my.profile');

    // Update nama & bio (profil publik)
    Route::patch('/my-profile/public', [ProfileController::class, 'updatePublicProfile'])->name('my.profile.update');

    // Update foto profil
    Route::post('/my-profile/photo', [ProfileController::class, 'updatePhoto'])->name('my.profile.photo');

    // Update background
    Route::post('/my-profile/background', [ProfileController::class, 'updateBackgroundPhoto'])->name('my.profile.background');
});

// search user
Route::get('/api/search-users', function(Request $request) {
    $q = $request->input('q');
    if (!$q) return [];

    return User::query()
        ->where('name', 'like', "%{$q}%")
        ->orWhere('role', 'like', "%{$q}%")
        ->limit(5)
        ->get(['id', 'name', 'role', 'bio', 'profile_photo_path', 'created_at']) // ambil created_at juga
        ->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'bio' => $user->bio,
                'trusted_writer' => $user->trusted_writer,
                'profile_photo_url' => $user->profile_photo_path
                    ? asset('storage/' . $user->profile_photo_path)
                    : null,
                'created_at' => $user->created_at?->toDateString(), // contoh: 2025-08-24
            ];
        });
});

//komen

Route::post('/articles/{article}/comments', [CommentController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');


//footer link about contact
Route::get('/about', function () {
    return Inertia::render('About');
})->name('about');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

Route::get('/privacy', function () {
    return Inertia::render('Privacy');
})->name('privacy');

Route::get('/terms', function () {
    return Inertia::render('Terms');
})->name('terms');

// === Auth routes dari Laravel Breeze ===
require __DIR__.'/auth.php';
