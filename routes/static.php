<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// === Static Pages (footer links) ===
Route::get('/about', fn () => Inertia::render('Static/About'))->name('about');
Route::get('/contact', fn () => Inertia::render('Static/Contact'))->name('contact');
Route::get('/privacy', fn () => Inertia::render('Static/Privacy'))->name('privacy');
Route::get('/terms', fn () => Inertia::render('Static/Terms'))->name('terms');
