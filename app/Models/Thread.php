<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Thread extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'slug', 'views'];

    // Relasi: thread dimiliki user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: thread punya banyak post
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // Generate slug otomatis dari title
    protected static function booted()
    {
        static::creating(function ($thread) {
            $thread->slug = Str::slug($thread->title) . '-' . Str::random(5);
        });
    }
}
