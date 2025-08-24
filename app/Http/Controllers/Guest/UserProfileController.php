<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Article;
use Inertia\Inertia;

class UserProfileController extends Controller
{
    public function show(User $user)
    {
      $approvedArticles = Article::where('status', 'approved')
    ->where('user_id', $user->id)
    ->latest()
    ->withCount('likes')
    ->get([
        'id',
        'title',
        'summary', // 👉 ini ditambah
        'cover',
        'category',
        'created_at',
        'updated_at',
        'hits',
    ]);

        return Inertia::render('guest/UserProfile', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'profile_photo_path' => $user->profile_photo_path,
                'background_photo_path' => $user->background_photo_path,
                'bio' => $user->bio,
            ],
            'articles' => $approvedArticles,
        ]);
    }
}
