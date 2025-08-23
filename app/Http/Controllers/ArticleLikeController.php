<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleLikeController extends Controller
{
    public function toggle(Request $request, Article $article)
    {
        $user = $request->user();

        // Kalau sudah like → hapus (unlike)
        if ($article->likedBy($user)) {
            $article->likes()->where('user_id', $user->id)->delete();
            return back()->with('message', 'Unliked');
        }

        // Kalau belum → buat like baru
        $article->likes()->create([
            'user_id' => $user->id,
        ]);

        return back()->with('message', 'Liked');
    }
}
