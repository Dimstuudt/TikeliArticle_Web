<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Article;
use App\Models\ArticleView;
use App\Models\ArticleLike;
use App\Models\Comment; // ✅ pastikan model Comment ada
use Inertia\Inertia;

class UserProfileController extends Controller
{
    public function show(User $user)
    {
        $approvedArticles = Article::where('status', 'approved')
            ->where('user_id', $user->id)
            ->latest()
            ->with('user') // ✅ ambil data author termasuk trusted_writer
            ->withCount('likes') // pastikan relasi likes() ada di model Article
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

        $articleIds = $approvedArticles->pluck('id');

        // 🔥 Ambil statistik
        $stats = [
            'hits'              => $approvedArticles->sum('hits'),
            'views'             => ArticleView::whereIn('article_id', $articleIds)->count(),
            'likes'             => ArticleLike::whereIn('article_id', $articleIds)->count(),
            'comments'          => Comment::whereIn('article_id', $articleIds)->count(), // ✅ jumlah komentar
            'approved_articles' => $approvedArticles->count(), // ✅ jumlah artikel approved
        ];

        return Inertia::render('guest/UserProfile', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'profile_photo_path' => $user->profile_photo_path,
                'background_photo_path' => $user->background_photo_path,
                'bio' => $user->bio,
                 'trusted_writer' => $user->trusted_writer, // ✅ tambahin ini
                'created_at' => $user->created_at
                    ? $user->created_at->timezone('Asia/Jakarta')->toDateString()
                    : null,
            ],
            'articles' => $approvedArticles,
            'stats'    => $stats,
        ]);
    }
}
