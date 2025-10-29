<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content'];

    // Each post belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Each post can have many comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Each post can have many likes
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Each post can have multiple media items
    public function media()
    {
        return $this->hasMany(Media::class);
    }
}
