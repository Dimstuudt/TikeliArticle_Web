<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Article;
use App\Models\ArticleView;
use App\Models\ArticleLike;
use App\Models\Comment;
use Inertia\Inertia;

class UserProfileController extends Controller
{
    public function show(User $user)
    {
        // Ambil artikel yang approved milik user
        $approvedArticles = Article::where('status', 'approved')
            ->where('user_id', $user->id)
            ->latest()
            ->with('user') // load author
            ->withCount('likes') // jumlah likes
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

        // Statistik user
        $stats = [
            'hits'              => $approvedArticles->sum('hits'),
            'views'             => ArticleView::whereIn('article_id', $articleIds)->count(),
            'likes'             => ArticleLike::whereIn('article_id', $articleIds)->count(),
            'comments'          => Comment::whereIn('article_id', $articleIds)->count(),
            'approved_articles' => $approvedArticles->count(),
        ];

        // Render ke Inertia
        return Inertia::render('guest/UserProfile', [
            'user' => [
                'id'                     => $user->id,
                'name'                   => $user->name,
                'roles_array'            => $user->getRoleNames(), // role Spatie
                'profile_photo_url'      => $user->profile_photo_url, // accessor otomatis fallback
                'background_photo_path'  => $user->background_photo_path
                                             ? asset('storage/' . $user->background_photo_path)
                                             : null,
                'bio'                    => $user->bio,
                'trusted_writer'         => $user->trusted_writer,
                'created_at'             => $user->created_at, // bisa diformat di Vue pakai dayjs
            ],
            'articles' => $approvedArticles,
            'stats'    => $stats,
        ]);
    }
}
