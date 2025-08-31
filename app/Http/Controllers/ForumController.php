<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ForumController extends Controller
{
    // List semua thread
 public function index()
{
    $threads = Thread::with(['user', 'posts.user'])
        ->latest() // terbaru duluan
        ->paginate(6);

    return Inertia::render('Forum/Index', [
        'threads' => $threads,
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


}
