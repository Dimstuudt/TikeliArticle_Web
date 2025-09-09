<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;

Route::get('/api/search-users', function(Request $request) {
    $q = $request->input('q');
    if (!$q) return [];

    return User::query()
        ->where('name', 'like', "%{$q}%")
        ->orWhereHas('roles', function($query) use ($q) {
            $query->where('name', 'like', "%{$q}%");
        })
        ->limit(5)
        ->get()
        ->map(fn($user) => [
            'id' => $user->id,
            'name' => $user->name,
            // 🔑 ambil role array dari spatie
            'roles' => $user->getRoleNames(),
            'bio' => $user->bio,
            'trusted_writer' => $user->trusted_writer ?? false,
            'profile_photo_url' => $user->profile_photo_path
                ? asset('storage/' . $user->profile_photo_path)
                : null,
            'created_at' => $user->created_at?->toDateString(),
        ]);
});

