<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ArticleController, ArticleLikeController, Guest\UserProfileController, CommentController};

// Landing
Route::get('/welcome', [ArticleController::class, 'landing'])->name('guest.welcome');

// Guest articles & profiles
Route::get('/articles/{id}', [ArticleController::class, 'guestShow'])->name('guest.articles.show');
Route::get('/users/{user}', [UserProfileController::class, 'show'])->name('guest.profile');

// Likes
Route::post('/articles/{article}/like', [ArticleLikeController::class, 'toggle'])->middleware('auth')->name('articles.like');

// Comments
Route::post('/articles/{article}/comments', [CommentController::class, 'store'])->middleware('auth')->name('comments.store');

// Laporan
use App\Http\Controllers\LaporanController;

Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');

