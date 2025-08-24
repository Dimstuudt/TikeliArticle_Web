<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

   protected $fillable = [
    'user_id',
    'title',
    'summary', // ← tambahkan ini
    'content',
    'status',
    'cover',
    'category',
];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //like
    public function likes()
{
    return $this->hasMany(ArticleLike::class);
}

public function likedBy(User $user)
{
    return $this->likes()->where('user_id', $user->id)->exists();
}

// User.php
public function articleLikes()
{
    return $this->hasManyThrough(ArticleLike::class, Article::class);
}


}
