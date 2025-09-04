<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Post;
use App\Models\User;
use App\Models\ArticleView; // pastikan import
use Illuminate\Http\Request;
use Inertia\Inertia;

class ForumController extends Controller
{
    // List semua thread + leaderboard
    public function index()
    {
        $threads = Thread::with(['user', 'posts.user'])
            ->latest()
            ->paginate(6);

        // Ambil leaderboard top 5
        $p = config('points');

        $leaderboard = User::withCount([
            'articles as approved_count' => fn($q) => $q->where('status', 'approved'),
            'articleLikes as total_likes',
        ])
        ->with(['articles' => fn($q) => $q->withCount('comments')])
        ->get()
        ->map(function ($u) use ($p) {
            // Ambil total hits dari kolom articles.hits
            $total_hits = $u->articles->sum('hits');

            // Ambil total views hanya dari user login (tabel article_views)
            $articleIds = $u->articles->pluck('id');
            $total_views = ArticleView::whereIn('article_id', $articleIds)->count();

            // Total comments dari withCount('comments')
            $total_comments = $u->articles->sum('comments_count');

            $u->total_points =
                ($u->approved_count ?? 0) * $p['approved'] +
                ($total_views ?? 0)    * $p['view'] +
                ($u->total_likes ?? 0) * $p['like'] +
                ($total_hits ?? 0)     * $p['hit'] +
                ($total_comments ?? 0) * $p['comment'];

            return $u;
        })
        ->sortByDesc('total_points')
        ->values()
        ->take(5); // top 5 user

        return Inertia::render('Forum/Index', [
            'threads' => $threads,
            'leaderboard' => $leaderboard,
        ]);
    }

    // Show detail thread + semua posts
    public function show(Thread $thread)
    {
        $thread->load('user')
               ->load(['posts' => function ($q) {
                   $q->with('user')->orderBy('created_at', 'asc');
               }]);

        return Inertia::render('Forum/Show', [
            'thread' => $thread,
        ]);
    }

    // Store thread baru
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body'  => 'required|string',
        ]);

        $thread = Thread::create([
            'user_id' => auth()->id(),
            'title'   => $request->title,
        ]);

        // Post pertama = isi thread
        Post::create([
            'thread_id' => $thread->id,
            'user_id'   => auth()->id(),
            'body'      => $request->body,
        ]);

        return redirect()
            ->route('forum.index')
            ->with('success', 'Thread berhasil dibuat!');
    }

    // Store komentar (balasan) ke thread
    public function reply(Request $request, Thread $thread)
    {
        $request->validate([
            'body' => 'required|string|max:5000',
        ]);

        $post = $thread->posts()->create([
            'user_id' => auth()->id(),
            'body'    => $request->body,
        ]);

        $post->load('user');

        return response()->json($post);
    }

    public function destroy(Thread $thread)
    {
        // Pastikan user adalah trusted_writer dan pemilik thread
        if (auth()->user()->id !== $thread->user_id || !auth()->user()->trusted_writer) {
            abort(403, 'Akses ditolak.');
        }

        $thread->delete();

        return back()->with('success', 'Thread berhasil dihapus!');
    }
}
