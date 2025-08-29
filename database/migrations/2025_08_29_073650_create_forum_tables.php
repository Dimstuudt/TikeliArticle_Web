<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // tabel threads
        Schema::create('threads', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('title');
            $t->string('slug')->unique();
            $t->unsignedInteger('views')->default(0);
            $t->timestamps();
        });

        // tabel posts (isi thread / balasan)
        Schema::create('posts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('thread_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->longText('body');
            $t->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('threads');
    }
};
