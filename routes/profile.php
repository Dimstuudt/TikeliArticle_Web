<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Middleware\{PreventBackHistory, EnsureProfileComplete};

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

// MyProfile - edit profil publik
Route::middleware(['auth'])->group(function () {
    Route::get('/my-profile', fn () => Inertia\Inertia::render('Profile/MyProfile'))->name('my.profile');
    Route::patch('/my-profile/public', [ProfileController::class, 'updatePublicProfile'])->name('my.profile.update');
    Route::post('/my-profile/photo', [ProfileController::class, 'updatePhoto'])->name('my.profile.photo');
    Route::post('/my-profile/background', [ProfileController::class, 'updateBackgroundPhoto'])->name('my.profile.background');
});
