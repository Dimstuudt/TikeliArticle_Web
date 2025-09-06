<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

// Controllers

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\DashboardController;


// Middleware
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\EnsureProfileComplete;
use App\Http\Middleware\RoleMiddleware;

// Models
use App\Models\User;
use App\Models\Article;

// Redirect root ke login
Route::get('/', fn () => redirect('/welcome'));


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








// === Auth routes dari Laravel Breeze ===
require __DIR__.'/auth.php';
require __DIR__.'/forum.php';
require __DIR__.'/guest.php';
require __DIR__.'/search.php';
require __DIR__.'/static.php';
require __DIR__.'/profile.php';
