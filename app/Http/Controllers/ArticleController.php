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
    public function mine()
    {
        $articles = auth()->user()->articles()->latest()->get();

        return Inertia::render('operator/Articles/Mine', [
            'articles' => $articles,
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
        $articles = Article::with('user')->latest()->get();

        return Inertia::render('admin/Articles/Index', [
            'articles' => $articles,
        ]);
    }

    // Admin - Setujui Artikel
    public function approve(Article $article)
    {
        $article->update(['status' => 'approved']);

        return back()->with('success', 'Artikel disetujui.');
    }

    // Admin - Tolak Artikel
    public function reject(Article $article)
    {
        $article->update(['status' => 'rejected']);

        return back()->with('success', 'Artikel ditolak.');
    }

    // Helper - Autentikasi untuk edit
    private function authorizeEdit(Article $article)
    {
        if ($article->user_id !== auth()->id() || !in_array($article->status, ['draft', 'rejected'])) {
            abort(403, 'Unauthorized');
        }
    }
}
