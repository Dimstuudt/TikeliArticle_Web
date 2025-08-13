<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
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
            'approved'     => Article::where('status', 'approved')->count(),
        ];

        // Rentang waktu 15 hari terakhir (termasuk hari ini)
        $startDate = Carbon::now()->subDays(14);
        $dates = collect();
        for ($i = 0; $i < 15; $i++) {
            $dates->push(Carbon::now()->subDays(14 - $i)->format('d M'));
        }

        // Data approved article
        $approvedRaw = Article::selectRaw('DATE(created_at) as tanggal, COUNT(*) as totalApproved')
            ->where('status', 'approved')
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::parse($item->tanggal)->format('d M') => $item->totalApproved];
            });

        // Data user baru
        $usersRaw = User::selectRaw('DATE(created_at) as tanggal, COUNT(*) as totalUsers')
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->mapWithKeys(function ($item) {
                return [Carbon::parse($item->tanggal)->format('d M') => $item->totalUsers];
            });

        // Gabungkan dengan daftar tanggal supaya tidak bolong
        $dailyApproved = $dates->map(function ($date) use ($approvedRaw) {
            return [
                'tanggal'       => $date,
                'totalApproved' => $approvedRaw[$date] ?? 0,
            ];
        });

        $dailyUsers = $dates->map(function ($date) use ($usersRaw) {
            return [
                'tanggal'    => $date,
                'totalUsers' => $usersRaw[$date] ?? 0,
            ];
        });

        return Inertia::render('admin/Dashboard', [
            'statsData'         => $statsData,
            'dailyApprovedData' => $dailyApproved,
            'dailyUsersData'    => $dailyUsers,
        ]);
    }
}
