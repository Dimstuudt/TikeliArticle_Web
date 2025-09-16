<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ApprovedArticleController extends Controller
{
    // Menampilkan daftar artikel approved (belum dihapus)
    public function index()
    {
        $articles = Article::where('status', 'approved')
            ->select('id', 'title', 'hits', 'created_at', 'category')
            ->latest()
            ->get();

        return Inertia::render('Admin/Articles/Approved', [
            'articles' => $articles,
        ]);
    }

    // Soft delete artikel approved
    public function destroy($id)
    {
        $article = Article::where('status', 'approved')->findOrFail($id);
        $article->delete();

        return redirect()->route('admin.approved-articles.index')
            ->with('success', 'Artikel berhasil dihapus (soft delete).');
    }

    // Menampilkan artikel yang sudah dihapus (trash)
    public function trashed()
    {
        $articles = Article::onlyTrashed()
            ->where('status', 'approved')
            ->select('id', 'title', 'hits', 'created_at', 'category', 'deleted_at')
            ->latest('deleted_at')
            ->get();

        return Inertia::render('Admin/Articles/Trashed', [
            'articles' => $articles,
        ]);
    }

    // Restore artikel dari trash
    public function restore($id)
    {
        $article = Article::onlyTrashed()
            ->where('status', 'approved')
            ->findOrFail($id);

        $article->restore();

        return redirect()->route('admin.approved-articles.trashed')
            ->with('success', 'Artikel berhasil direstore.');
    }

    // Hard delete permanen dari trash
    public function forceDelete($id)
    {
        $article = Article::onlyTrashed()
            ->where('status', 'approved')
            ->findOrFail($id);

        $article->forceDelete();

        return redirect()->route('admin.approved-articles.trashed')
            ->with('success', 'Artikel berhasil dihapus permanen.');
    }

   public function bulkDestroy(Request $request)
{
    $request->validate([
        'ids' => 'required|array',
    ]);

    // Soft delete
    Article::whereIn('id', $request->ids)->delete();

    return back()->with('success', 'Artikel berhasil dipindahkan ke trash.');
}

public function bulkRestore(Request $request)
{
    $request->validate([
        'ids' => 'required|array',
    ]);

    Article::onlyTrashed()->whereIn('id', $request->ids)->restore();

    return back()->with('success', 'Artikel berhasil direstore.');
}

public function bulkForceDelete(Request $request)
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return response()->json(['message' => 'Tidak ada artikel yang dipilih.'], 400);
    }

    // Cari artikel yang sudah dihapus (soft deleted) dan force delete
    Article::onlyTrashed()
        ->whereIn('id', $ids)
        ->forceDelete();

    return back()->with('success', count($ids) . ' artikel berhasil dihapus permanen.');
}


}
