<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ubah kolom role menjadi enum
            $table->enum('role', ['super-admin', 'admin', 'operator'])->default('operator')->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // rollback jadi string lagi
            $table->string('role')->default('operator')->change();
        });
    }
};
