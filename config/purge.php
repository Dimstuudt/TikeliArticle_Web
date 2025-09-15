<?php

return [
    // Default berapa hari data soft delete disimpan
    'default_days' => env('PURGE_SOFT_DELETED_DAYS', 30),

    // Daftar model spesifik dengan lama simpan berbeda
    // Format: Model::class => jumlah_hari
    'models' => [
        // Contoh:
        // App\Models\User::class => 365,
        // App\Models\Article::class => 30,
    ],
];
