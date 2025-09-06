<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function store(Request $request)
    {
        // Validasi data
        $validated = $request->validate([
            'email'   => 'required|email|max:255',
            'laporan' => 'required|string',
        ]);

        // Simpan ke DB
        Laporan::create($validated);

        // Balikan response JSON (biar gampang ditangkap Vue)
        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dikirim!',
        ]);
    }
}
