<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('articles', function ($table) {
        $table->enum('category', [
            'ulasan', 'teknologi', 'hiburan', 'hoby', 'opini', 'fiksi', 'tutorial', 'biografi', 'comparison'
        ])->default('ulasan');
    });
}

public function down()
{
    Schema::table('articles', function ($table) {
        $table->dropColumn('category');
    });
}

};
