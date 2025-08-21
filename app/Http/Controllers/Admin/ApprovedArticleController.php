<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Inertia\Inertia;

class ApprovedArticleController extends Controller
{
    // Menampilkan daftar artikel approved
    public function index()
    {
        $articles = Article::where('status', 'approved')
    ->select('id', 'title','hits', 'created_at','category')
    ->latest()
    ->get();


        return Inertia::render('Admin/Articles/Approved', [
            'articles' => $articles
        ]);
    }

    // Hapus artikel approved
  public function destroy($id)
{
    $article = Article::where('status', 'approved')->findOrFail($id);
    $article->delete(); // Soft delete. Kalau mau hard delete pakai ->forceDelete()

    return redirect()->route('admin.approved-articles.index')
        ->with('success', 'Artikel berhasil dihapus.');
}


}
