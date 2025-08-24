<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Article;
use App\Models\ArticleView;
use App\Models\ArticleLike; // ✅ ganti dari Like ke ArticleLike
use Inertia\Inertia;

class UserProfileController extends Controller
{
    public function show(User $user)
    {
        $approvedArticles = Article::where('status', 'approved')
            ->where('user_id', $user->id)
            ->latest()
            ->withCount('likes') // ini akan jalan kalau relasi likes() udah ada di model Article
            ->get([
                'id',
                'title',
                'summary',
                'cover',
                'category',
                'created_at',
                'updated_at',
                'hits',
            ]);

        // 🔥 Ambil statistik
        $stats = [
            'hits'  => $approvedArticles->sum('hits'),
            'views' => ArticleView::whereIn('article_id', $approvedArticles->pluck('id'))->count(),
            'likes' => ArticleLike::whereIn('article_id', $approvedArticles->pluck('id'))->count(), // ✅ udah bener
        ];

        return Inertia::render('guest/UserProfile', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'profile_photo_path' => $user->profile_photo_path,
                'background_photo_path' => $user->background_photo_path,
                'bio' => $user->bio,
                'created_at' => $user->created_at
                    ? $user->created_at->timezone('Asia/Jakarta')->toDateString()
                    : null,
            ],
            'articles' => $approvedArticles,
            'stats'    => $stats,
        ]);
    }
}
