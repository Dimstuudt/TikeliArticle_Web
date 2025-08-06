<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ArticleController extends Controller
{
    // Operator - Form Buat Artikel
    public function create()
    {
        return Inertia::render('operator/Articles/Create');
    }

    // Operator - Simpan Artikel Baru
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,draft',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

        if ($request->status === 'pending') {
            $rules['summary'] = 'required|string|max:500';
            $rules['content'] = 'required|string';
        } else {
            $rules['summary'] = 'nullable|string|max:500';
            $rules['content'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        $data = $validated;
        $data['user_id'] = Auth::id();

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        }

        Article::create($data);

        $message = $validated['status'] === 'draft'
            ? 'Artikel disimpan sebagai draft.'
            : 'Artikel berhasil dikirim.';

        return redirect()->route('operator.articles.mine')->with('success', $message);
    }

    // Operator - Lihat Artikel Milik Sendiri
public function mine(Request $request)
{
    // Ambil filter status dari query string (array atau string)
    $statusParam = $request->query('status', []);
    $statuses = is_array($statusParam)
        ? $statusParam
        : explode(',', $statusParam);

    // Ambil keyword pencarian dari query string
    $search = $request->query('search');

    // Ambil query artikel milik user yang sedang login
    $query = auth()->user()->articles()->latest();

    // Filter berdasarkan status jika ada
    if (!empty($statuses)) {
        $query->whereIn('status', $statuses);
    }

    // Filter berdasarkan judul jika search tidak kosong
    if (!empty($search)) {
        $query->where('title', 'like', '%' . $search . '%');
    }

    // Ambil dan format data artikel
    $articles = $query->get()->map(function ($article) {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'summary' => $article->summary,
            'status' => $article->status,
            'cover_url' => $article->cover ? asset('storage/' . $article->cover) : null,
            'created_at' => $article->created_at,
            'rejection_reason' => $article->rejection_reason,
        ];
    });

    // Hitung jumlah artikel berdasarkan status
    $stats = auth()->user()->articles()
        ->selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status');

    // Kirim data ke tampilan
    return Inertia::render('operator/Articles/Mine', [
        'articles' => $articles,
        'filters' => [
            'status' => $statuses,
            'search' => $search,
        ],
        'stats' => $stats,
    ]);
}



    // Operator - Form Edit Artikel
    public function edit(Article $article)
    {
        $this->authorizeEdit($article);

        return Inertia::render('operator/Articles/Edit', [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'summary' => $article->summary,
                'content' => $article->content,
                'status' => $article->status,
                'cover_url' => $article->cover ? asset('storage/' . $article->cover) : null,
            ],
        ]);
    }

    // Operator - Update Artikel
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $this->authorizeEdit($article);

        $rules = [
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,draft',
            'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

        if ($request->status === 'pending') {
            $rules['summary'] = 'required|string|max:500';
            $rules['content'] = 'required|string';
        } else {
            $rules['summary'] = 'nullable|string|max:500';
            $rules['content'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        $article->title = $validated['title'];
        $article->summary = $validated['summary'] ?? null;
        $article->content = $validated['content'] ?? null;
        $article->status = $validated['status'];

        if ($request->hasFile('cover')) {
            if ($article->cover && Storage::disk('public')->exists($article->cover)) {
                Storage::disk('public')->delete($article->cover);
            }
            $article->cover = $request->file('cover')->store('covers', 'public');
        }

        $article->save();

        return redirect()->route('operator.articles.mine')->with('success', 'Artikel berhasil diperbarui.');
    }

    // Operator - Hapus Artikel
    public function destroy(Article $article)
    {
        $this->authorizeEdit($article);

        if ($article->cover && Storage::disk('public')->exists($article->cover)) {
            Storage::disk('public')->delete($article->cover);
        }

        $article->delete();

        return redirect()->route('operator.articles.mine')->with('success', 'Artikel berhasil dihapus.');
    }

    // Admin - Lihat Semua Artikel
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

    public function reject(Request $request, $id)
{
    $request->validate([
        'reason' => 'required|string|max:1000',
    ]);

    $article = Article::findOrFail($id);
    $article->status = 'rejected';
    $article->rejection_reason = $request->reason;
    $article->save();

    return back()->with('message', 'Artikel berhasil ditolak.');
}


    // Helper - Autentikasi untuk edit
    private function authorizeEdit(Article $article)
    {
        if ($article->user_id !== auth()->id() || !in_array($article->status, ['draft', 'rejected', 'pending'])) {
            abort(403, 'Unauthorized');
        }
    }

public function saveEdit(Request $request, Article $article)
{
    $this->authorizeEdit($article);

    $rules = [
        'title' => 'required|string|max:255',
        'status' => 'required|in:pending,draft',
        'cover' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ];

    if ($request->status === 'pending') {
        $rules['summary'] = 'required|string|max:500';
        $rules['content'] = 'required|string';
    } else {
        $rules['summary'] = 'nullable|string|max:500';
        $rules['content'] = 'nullable|string';
    }

    $validated = $request->validate($rules);

    $article->fill([
        'title' => $validated['title'],
        'summary' => $validated['summary'] ?? null,
        'content' => $validated['content'] ?? null,
        'status' => $validated['status'],
    ]);

    if ($request->hasFile('cover')) {
        if ($article->cover && Storage::disk('public')->exists($article->cover)) {
            Storage::disk('public')->delete($article->cover);
        }
        $article->cover = $request->file('cover')->store('covers', 'public');
    }

    $article->save();

    return redirect()->route('operator.articles.mine')->with('success', 'Artikel berhasil diperbarui.');
}

public function show(Article $article)
{
    return Inertia::render('admin/articles/Show', [
        'article' => [
            'id' => $article->id,
            'title' => $article->title,
            'cover_url' => $article->cover ? asset('storage/' . $article->cover) : null,
            'summary' => $article->summary,
            'content' => $article->content,
            'status' => $article->status,
            'created_at' => $article->created_at,
        ],
    ]);
}

//tampilan

public function landing()
{
    $articles = Article::where('status', 'approved')
        ->with('user') // untuk ambil nama penulis
        ->latest()
        ->take(6)
        ->get()
        ->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'summary' => $article->summary,
                'content' => $article->content,
                'cover' => $article->cover ? asset('storage/' . $article->cover) : null,
                'created_at' => $article->created_at, // kirim datetime asli
                'updated_at' => $article->updated_at, // kirim datetime asli
                'author' => [
                    'id' => $article->user->id,
                    'name' => $article->user->name,
                ],
            ];
        });

    return Inertia::render('welcome', [
        'articles' => $articles,
    ]);
}

public function guestShow($id)
{
    $article = Article::with('user')->where('status', 'approved')->findOrFail($id);

    return Inertia::render('guest/Articles/Show', [
        'article' => [
            'id' => $article->id,
            'title' => $article->title,
            'summary' => $article->summary,
            'content' => $article->content,
            'cover' => $article->cover ? asset('storage/' . $article->cover) : null,
            'created_at' => $article->created_at->diffForHumans(),
            'author' => [
                'id' => $article->user->id,
                'name' => $article->user->name,
            ],
        ],
    ]);
}


}
