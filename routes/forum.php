<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForumController;

Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::get('/forum/{thread}', [ForumController::class, 'show'])->name('forum.show');
Route::post('/forum', [ForumController::class, 'store'])->middleware('auth')->name('forum.store');
Route::post('/forum/{thread}/reply', [ForumController::class, 'reply'])->middleware('auth')->name('forum.reply');

