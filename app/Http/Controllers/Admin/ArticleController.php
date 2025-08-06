<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('user')->latest()->get()->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'summary' => $article->summary,
                'content' => $article->content,
                'status' => $article->status,
                'cover_url' => $article->cover ? asset('storage/' . $article->cover) : null,
                'created_at' => $article->created_at->toDateTimeString(),
                'user' => [
                    'id' => $article->user->id,
                    'name' => $article->user->name,
                ],
            ];
        });

        return Inertia::render('admin/Articles/Index', [
            'articles' => $articles,
        ]);
    }

    public function approve($id)
    {
        $article = Article::findOrFail($id);
        $article->status = 'approved';
        $article->save();

        return redirect()->back();
    }

    public function reject($id)
    {
        $article = Article::findOrFail($id);
        $article->status = 'rejected';
        $article->save();

        return redirect()->back();
    }
}
