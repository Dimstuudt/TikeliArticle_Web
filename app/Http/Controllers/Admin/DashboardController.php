<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    // === DASHBOARD ADMIN ===
    public function adminIndex()
    {
        $statsData = [
            'totalArtikel' => Article::count(),
            'pending'      => Article::where('status', 'pending')->count(),
            'totalUser'    => User::count(),
            'kategori'     => Article::distinct('category')->count('category'),
            'approved'     => Article::where('status', 'approved')->count(),
        ];

        // Rentang 15 hari terakhir
        $startDate = Carbon::now()->subDays(14);
        $dates = collect(range(0, 14))->map(fn($i) => Carbon::now()->subDays(14 - $i)->format('d M'));

        // Data approved article (per hari)
        $approvedRaw = Article::selectRaw('DATE(created_at) as tanggal, COUNT(*) as totalApproved')
            ->where('status', 'approved')
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->mapWithKeys(fn($item) => [Carbon::parse($item->tanggal)->format('d M') => $item->totalApproved]);

        // Data user baru (per hari)
        $usersRaw = User::selectRaw('DATE(created_at) as tanggal, COUNT(*) as totalUsers')
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->mapWithKeys(fn($item) => [Carbon::parse($item->tanggal)->format('d M') => $item->totalUsers]);

        // Lengkapi tanggal kosong
        $dailyApproved = $dates->map(fn($date) => [
            'tanggal'       => $date,
            'totalApproved' => $approvedRaw[$date] ?? 0,
        ]);

        $dailyUsers = $dates->map(fn($date) => [
            'tanggal'    => $date,
            'totalUsers' => $usersRaw[$date] ?? 0,
        ]);

        return Inertia::render('Admin/Dashboard', [
            'statsData'         => $statsData,
            'dailyApprovedData' => $dailyApproved,
            'dailyUsersData'    => $dailyUsers,
        ]);
    }

    // === DASHBOARD OPERATOR ===
    public function operatorIndex()
    {
        $userId = auth()->id();

        $statsData = [
            'totalArtikel' => Article::where('user_id', $userId)->count(),
            'approved'     => Article::where('user_id', $userId)->where('status', 'approved')->count(),
            'pending'      => Article::where('user_id', $userId)->where('status', 'pending')->count(),
            'rejected'     => Article::where('user_id', $userId)->where('status', 'rejected')->count(),
            'totalViews'   => Article::where('user_id', $userId)->sum('hits'),
        ];

        $startDate = Carbon::now()->subDays(14);
        $dates = collect(range(0, 14))->map(fn($i) => Carbon::now()->subDays(14 - $i)->format('d M'));

        // Artikel dibuat per hari
        $createdRaw = Article::selectRaw('DATE(created_at) as tanggal, COUNT(*) as totalCreated')
            ->where('user_id', $userId)
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->mapWithKeys(fn($item) => [Carbon::parse($item->tanggal)->format('d M') => $item->totalCreated]);

        // Artikel disetujui per hari
        $approvedRaw = Article::selectRaw('DATE(updated_at) as tanggal, COUNT(*) as totalApproved')
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->whereDate('updated_at', '>=', $startDate)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->mapWithKeys(fn($item) => [Carbon::parse($item->tanggal)->format('d M') => $item->totalApproved]);

        // Lengkapi tanggal kosong
        $dailyCreated = $dates->map(fn($date) => [
            'tanggal'      => $date,
            'totalCreated' => $createdRaw[$date] ?? 0,
        ]);

        $dailyApproved = $dates->map(fn($date) => [
            'tanggal'       => $date,
            'totalApproved' => $approvedRaw[$date] ?? 0,
        ]);

        return Inertia::render('Operator/Dashboard', [
            'statsData'        => $statsData,
            'dailyCreatedData' => $dailyCreated,
            'dailyApprovedData'=> $dailyApproved,
        ]);
    }
}
