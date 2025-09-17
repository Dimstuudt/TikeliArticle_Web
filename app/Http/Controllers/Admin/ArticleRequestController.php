<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArticleRequestController extends Controller
{
    // List semua request
    public function index()
    {
        $requests = ArticleRequest::with(['article', 'user'])
            ->latest()
            ->get();

        return Inertia::render('Admin/Articles/Index', [
            'requests' => $requests,
        ]);
    }

    // Approve / Reject
    public function update(Request $request, $id)
    {
        $articleRequest = ArticleRequest::with('article')->findOrFail($id);

        $action = $request->input('action'); // "approve" atau "reject"

        if ($action === 'approve') {
            if ($articleRequest->type === 'to_draft') {
                $articleRequest->article->update(['status' => 'draft']);
            } elseif ($articleRequest->type === 'delete') {
                $articleRequest->article->delete();
            }
            $articleRequest->status = 'approved';
        } else {
            $articleRequest->status = 'rejected';
        }

        $articleRequest->save();

        return redirect()->back()->with('success', 'Request berhasil diproses.');
    }
}
