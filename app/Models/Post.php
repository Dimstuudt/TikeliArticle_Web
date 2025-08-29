<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['thread_id', 'user_id', 'body'];

    // Relasi: post dimiliki user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: post milik thread
    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }
}
