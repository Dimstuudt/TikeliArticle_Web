<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Report;

class ReportController extends Controller
{
    public function index()
    {
        // Ambil data dari tabel laporans
        $reports = DB::table('laporans')->latest()->paginate(10);

        return Inertia::render('Admin/Reports/Index', [
            'reports' => $reports,
        ]);
    }

    public function destroy(Report $report)
{
    $report->delete();

    return redirect()->route('admin.reports.index')
        ->with('success', 'Laporan berhasil dihapus.');
}

}


