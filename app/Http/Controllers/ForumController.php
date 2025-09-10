<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Post;
use App\Models\User;
use App\Models\ArticleView;
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
        ->with(['articles' => fn($q) => $q->withCount('comments'), 'roles']) // ← tambahkan roles
        ->get()
        ->map(function ($u) use ($p) {
            $total_hits = $u->articles->sum('hits');
            $articleIds = $u->articles->pluck('id');
            $total_views = ArticleView::whereIn('article_id', $articleIds)->count();
            $total_comments = $u->articles->sum('comments_count');

            $u->total_points =
                ($u->approved_count ?? 0) * $p['approved'] +
                ($total_views ?? 0)    * $p['view'] +
                ($u->total_likes ?? 0) * $p['like'] +
                ($total_hits ?? 0)     * $p['hit'] +
                ($total_comments ?? 0) * $p['comment'];

            // Ambil nama role pertama dari Spatie
            $u->role = $u->roles->pluck('name')->first() ?? 'User';

            return $u;
        })
        ->sortByDesc('total_points')
        ->values()
        ->take(5); // top 5 user

        return Inertia::render('Forum/Index', [
            'threads' => $threads,
            'leaderboard' => $leaderboard,
            'authUserId' => auth()->id(), // buat highlight di frontend
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
