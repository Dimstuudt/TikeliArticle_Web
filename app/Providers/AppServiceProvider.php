<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Skip share auth data saat artisan CLI dijalankan
        if ($this->app->runningInConsole()) {
            return;
        }

        Inertia::share([
            // Data auth
            'auth' => fn () => [
                'user' => Auth::check() ? array_merge(
                    Auth::user()->only([
                        'id',
                        'name',
                        'email',
                        'username',
                        'profile_photo_path'
                    ]),
                    ['profile_photo_url' => Auth::user()->profile_photo_url]
                ) : null,
            ],

            // Statistik global
            'stats' => fn () => [
                [
                    'id' => 'articles',
                    'label' => 'Artikel',
                    'display' => \App\Models\Article::where('status', 'approved')->count(),

                ],
                [
                    'id' => 'users',
                    'label' => 'Pengguna',
                    'display' => User::count(),
                ],
                [
                    'id' => 'comments',
                    'label' => 'Komentar',
                    'display' => Comment::count(),
                ],
                [
                    'id' => 'readers',
                    'label' => 'Pembaca',
                    'display' => DB::table('article_views')->count(),
                ],
            ],
        ]);
    }
}
