<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('articles', function (Blueprint $table) {
        $table->enum('request_action', ['none', 'delete', 'revert'])->default('none');
        $table->text('request_note')->nullable();
    });
}

public function down(): void
{
    Schema::table('articles', function (Blueprint $table) {
        $table->dropColumn(['request_action', 'request_note']);
    });
}

};
