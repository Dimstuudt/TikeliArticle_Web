<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;

Route::get('/api/search-users', function(Request $request) {
    $q = $request->input('q');
    if (!$q) return [];

    return User::query()
        ->where('name', 'like', "%{$q}%")
        ->orWhere('role', 'like', "%{$q}%")
        ->limit(5)
        ->get(['id', 'name', 'role', 'bio', 'profile_photo_path', 'created_at'])
        ->map(fn($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'bio' => $user->bio,
            'trusted_writer' => $user->trusted_writer,
            'profile_photo_url' => $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : null,
            'created_at' => $user->created_at?->toDateString(),
        ]);
});
