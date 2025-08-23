<?php

namespace App\Http\Controllers;
use App\Models\User;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    // Operator - Form Buat Artikel
    public function create()
    {
        return Inertia::render('Operator/Articles/Create', [
            'categories' => $this->categories(),
        ]);
    }

    // Operator - Simpan Artikel Baru
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,draft',
            'category' => 'nullable|string|in:' . implode(',', $this->categories()),
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
        $statusParam = $request->query('status', []);
        $statuses = is_array($statusParam) ? $statusParam : explode(',', $statusParam);
        $search = $request->query('search');

        $query = auth()->user()->articles()->latest();

        if (!empty($statuses)) {
            $query->whereIn('status', $statuses);
        }

        if (!empty($search)) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        $articles = $query->get()->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'summary' => $article->summary,
                'status' => $article->status,
                'category' => $article->category,
                'cover_url' => $article->cover ? asset('storage/' . $article->cover) : null,
                'created_at' => $article->created_at,
                'rejection_reason' => $article->rejection_reason,
            ];
        });

        $stats = auth()->user()->articles()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return Inertia::render('Operator/Articles/Mine', [
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

        return Inertia::render('Operator/Articles/Edit', [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'summary' => $article->summary,
                'content' => $article->content,
                'status' => $article->status,
                'category' => $article->category,
                'cover_url' => $article->cover ? asset('storage/' . $article->cover) : null,
            ],
            'categories' => $this->categories(),
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
            'category' => 'nullable|string|in:' . implode(',', $this->categories()),
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
        $article->category = $validated['category'] ?? null;

        if ($request->hasFile('cover')) {
            if ($article->cover && Storage::disk('public')->exists($article->cover)) {
                Storage::disk('public')->delete($article->cover);
            }
            $article->cover = $request->file('cover')->store('covers', 'public');
        }

        $article->save();

        return redirect()->route('operator.articles.mine')->with('success', 'Artikel berhasil diperbarui.');
    }

    // Save edit (alternatif route)
    public function saveEdit(Request $request, Article $article)
    {
        $this->authorizeEdit($article);

        $rules = [
            'title' => 'required|string|max:255',
            'status' => 'required|in:pending,draft',
            'category' => 'nullable|string|in:' . implode(',', $this->categories()),
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
            'category' => $validated['category'] ?? null,
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
                'category' => $article->category,
                'cover_url' => $article->cover ? asset('storage/' . $article->cover) : null,
                'created_at' => $article->created_at->toDateTimeString(),
                'user' => [
                    'id' => $article->user->id,
                    'name' => $article->user->name,
                ],
            ];
        });

        return Inertia::render('Admin/Articles/Index', [
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

    public function show(Article $article)
    {
        return Inertia::render('Admin/Articles/Show', [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'summary' => $article->summary,
                'content' => $article->content,
                'status' => $article->status,
                'category' => $article->category,
                'cover_url' => $article->cover ? asset('storage/' . $article->cover) : null,
                'created_at' => $article->created_at,
            ],
        ]);
    }

  public function landing(Request $request)
{
    // Query utama untuk artikel paginasi
    $query = Article::where('status', 'approved')
        ->with('user')
        ->latest();

    // Filter pencarian
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%')
              ->orWhere('summary', 'like', '%' . $request->search . '%')
              ->orWhere('content', 'like', '%' . $request->search . '%');
        });
    }

    // Filter kategori
    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

    // Pagination 6 per halaman + query string
    $articles = $query->paginate(6)
        ->withQueryString()
        ->through(fn($article) => [
            'id' => $article->id,
            'title' => $article->title,
            'summary' => $article->summary,
            'content' => $article->content,
            'category' => $article->category,
             'hits'  => $article->hits,
        'views' => DB::table('article_views')->where('article_id', $article->id)->count(),
          'likes' => $article->likes()->count(), // 🔥 jumlah like
            'cover' => $article->cover ? asset('storage/' . $article->cover) : null,
            'updated_at' => $article->updated_at->toISOString(),
            'created_at' => $article->created_at->diffForHumans(),
            'author' => [
                'id' => $article->user->id,
                'name' => $article->user->name,
                'role' => $article->user->role,
                'profile_photo_path' => $article->user->profile_photo_path,
            ],
        ]);

    // Ambil 3 user terbaru
    $latestUsers = User::latest()
        ->take(3)
        ->get(['id', 'name', 'role', 'profile_photo_path']);

    // Ambil 3 artikel terhits berdasarkan kolom hits
    $topArticles = Article::where('status', 'approved')
    ->with('user')
    ->orderByDesc('hits')
    ->take(3)
    ->get()
    ->map(fn($article) => [
        'id' => $article->id,
        'title' => $article->title,
        'summary' => $article->summary,
          'likes' => $article->likes()->count(), // 🔥 jumlah like
        'cover' => $article->cover ? asset('storage/' . $article->cover) : null,
        'hits' => $article->hits,
        'created_at' => $article->created_at->toISOString(),
        'updated_at' => $article->updated_at->toISOString(),
        'author' => [
            'id' => $article->user->id,
            'name' => $article->user->name,
            'role' => $article->user->role,
            'profile_photo_path' => $article->user->profile_photo_path,
        ],
    ]);

    return Inertia::render('guest/Welcome', [
        'articles' => $articles,
        'latestUsers' => $latestUsers,
        'topArticles' => $topArticles,
        'filters' => [
            'search' => $request->search,
            'category' => $request->category,
        ],
    ]);
}

   // Guest - Lihat Artikel
public function guestShow($id)
{
    $article = Article::with('user')
        ->where('status', 'approved')
        ->findOrFail($id);

    /** 🔹 1. Tambah hits tanpa ubah updated_at */
    $article->timestamps = false;
    $article->increment('hits');

    /** 🔹 2. Refresh model */
    $article->refresh();

    /** 🔹 3. Tambah views unik kalau user login */
    if (auth()->check()) {
        $exists = DB::table('article_views')
            ->where('article_id', $article->id)
            ->where('user_id', auth()->id())
            ->exists();

        if (! $exists) {
            DB::table('article_views')->insert([
                'article_id' => $article->id,
                'user_id'    => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /** 🔹 4. Hitung total views unik */
    $viewsCount = DB::table('article_views')
        ->where('article_id', $article->id)
        ->count();

    /** 🔹 5. Ambil 4 rekomendasi artikel lain */
    $recommendations = Article::with('user')
        ->where('status', 'approved')
        ->where('id', '!=', $article->id)
        ->inRandomOrder()
        ->take(4)
        ->get()
        ->map(function ($rec) {
            return [
                'id'         => $rec->id,
                'title'      => $rec->title,
                'category'   => $rec->category,
                'cover'      => $rec->cover ? asset('storage/' . $rec->cover) : null,
                'created_at' => $rec->created_at,
                'views'      => DB::table('article_views')
                                    ->where('article_id', $rec->id)
                                    ->count(),
                'author'     => [
                    'id'   => $rec->user->id,
                    'name' => $rec->user->name,
                ],
            ];
        });

    /** 🔹 6. Likes (total + apakah user sudah like) */
    $likeCount = $article->likes()->count();
    $isLiked   = auth()->check() ? $article->likedBy(auth()->user()) : false;

    /** 🔹 7. Kirim ke Inertia */
    return Inertia::render('guest/Articles/Show', [
        'article' => [
            'id'         => $article->id,
            'title'      => $article->title,
            'summary'    => $article->summary,
            'content'    => $article->content,
            'category'   => $article->category,
            'cover'      => $article->cover ? asset('storage/' . $article->cover) : null,
            'created_at' => $article->created_at,
            'updated_at' => $article->updated_at,
            'hits'       => $article->hits,
            'author'     => [
                'id'                 => $article->user->id,
                'name'               => $article->user->name,
                'bio'                => $article->user->bio,
                'role'               => $article->user->role,
                'profile_photo_path' => $article->user->profile_photo_path,
            ],
        ],
        'views'           => $viewsCount,
        'likeCount'       => $likeCount,
        'isLiked'         => $isLiked,
        'from'            => request('from'),
        'recommendations' => $recommendations,
    ]);
}

    private function authorizeEdit(Article $article)
    {
        if ($article->user_id !== auth()->id() || !in_array($article->status, ['draft', 'rejected', 'pending'])) {
            abort(403, 'Unauthorized');
        }
    }

    private function categories(): array
    {
        return [
            'ulasan',
            'teknologi',
            'hiburan',
            'hoby',
            'opini',
            'fiksi',
            'tutorial',
            'biografi',
            'comparison',
        ];
    }

//aproved
public function approved()
{
    $articles = Article::where('status', 'approved')->latest()->get();
    return Inertia::render('Admin/Approved', [
        'articles' => $articles
    ]);
}

public function destroy($id)
{
    $article = Article::findOrFail($id);
    $article->delete();

    return redirect()->back()->with('success', 'Artikel berhasil dihapus');
}



}
