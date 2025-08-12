<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $statsData = [
            'totalArtikel' => Article::count(),
            'pending'      => Article::where('status', 'pending')->count(),
            'totalUser'    => User::count(),
            'kategori'     => Article::distinct('category')->count('category'),
            'approved'     => Article::where('status', 'approved')->count(), // tambahan
        ];

        // Ambil data approved article 15 hari terakhir
        $startDate = Carbon::now()->subDays(14); // termasuk hari ini = 15 hari
        $dailyApproved = Article::selectRaw('DATE(created_at) as tanggal, COUNT(*) as totalApproved')
    ->where('status', 'approved')
    ->whereDate('created_at', '>=', $startDate)
    ->groupBy('tanggal')
    ->orderBy('tanggal')
    ->get()
    ->map(function ($item) {
        return [
            'tanggal'       => Carbon::parse($item->tanggal)->format('d M'),
            'totalApproved' => $item->totalApproved,
        ];
    });


        return Inertia::render('admin/Dashboard', [
            'statsData'         => $statsData,
            'dailyApprovedData' => $dailyApproved,
        ]);
    }
}
