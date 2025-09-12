<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

// Controllers
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ApprovedArticleController;
use App\Http\Controllers\Admin\RolePermissionController;

// Middleware
use App\Http\Middleware\PreventBackHistory;
use App\Http\Middleware\EnsureProfileComplete;

// Redirect root ke welcome
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
            $user->hasRole('admin') ? 'admin.dashboard' : 'operator.dashboard'
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
        $user->hasAnyRole('admin', 'super-admin') ? 'admin.dashboard' : 'operator.dashboard'
    );
})->name('dashboard');

// === ADMIN Routes ===
Route::middleware([
    'auth',
    'verified',
    PreventBackHistory::class,
    EnsureProfileComplete::class,
    'role:admin|super-admin',
])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'adminIndex'])->name('dashboard');

    // ===============================
    // 🔹 REPORTS
    // ===============================
    Route::middleware(['permission:view reports'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });
    Route::middleware(['permission:delete reports'])->group(function () {
        Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
    });

    // ===============================
// 🔹 USER MANAGEMENT
// ===============================
Route::middleware('permission:manage users')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Toggle aktif/nonaktif & trusted writer
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggleActive');
    Route::patch('/users/{user}/toggle-trusted', [UserController::class, 'toggleTrusted'])->name('users.toggleTrusted');

    // 🔹 Trash & Restore
    Route::get('/users/trashed', [UserController::class, 'trashed'])->name('users.trashed');
    Route::put('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('/users/{id}/force', [UserController::class, 'forceDelete'])->name('users.forceDelete');
});


  // Approved Articles
        Route::prefix('articles/approved')->name('approved-articles.')->group(function () {
            Route::get('/', [ApprovedArticleController::class, 'index'])->name('index');
            Route::delete('/{id}', [ApprovedArticleController::class, 'destroy'])->name('destroy');

            // Trashed (soft deleted)
            Route::get('/trashed', [ApprovedArticleController::class, 'trashed'])->name('trashed');
            Route::put('/{id}/restore', [ApprovedArticleController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force', [ApprovedArticleController::class, 'forceDelete'])->name('forceDelete');
        });

    // ===============================
    // 🔹 ARTICLE MANAGEMENT
    // ===============================
    Route::middleware('permission:manage articles')->group(function () {
        // Semua artikel
        Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
        Route::put('/articles/{article}/approve', [ArticleController::class, 'approve'])->name('articles.approve');
        Route::put('/articles/{article}/reject', [ArticleController::class, 'reject'])->name('articles.reject');
        Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
        Route::delete('/articles/{id}', [ArticleController::class, 'destroy'])->name('articles.destroy');


    });

    // ===============================
    // 🔹 ROLE & PERMISSION MANAGEMENT
    // ===============================
    Route::middleware('permission:manage permissions')->group(function () {
        Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles-permissions');

        // Role CRUD
        Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
        Route::put('/roles/{role}', [RolePermissionController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [RolePermissionController::class, 'destroyRole'])->name('roles.destroy');

        // Permission CRUD
        Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
        Route::delete('/permissions/{permission}', [RolePermissionController::class, 'destroyPermission'])->name('permissions.destroy');
    });
});

// === OPERATOR Routes ===
Route::middleware([
    'auth',
    'verified',
    PreventBackHistory::class,
    EnsureProfileComplete::class,
    'role:operator|super-admin',
])->prefix('operator')->name('operator.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'operatorIndex'])->name('dashboard');

    // Artikel operator
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('articles.create');
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
