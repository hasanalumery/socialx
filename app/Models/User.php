<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * One-to-one relationship with Profile.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * One-to-many relationship with Like.
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * One-to-many relationship with Post.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * One-to-many relationship with Comment.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    

    /**
     * Users that this user is following.
     */
    public function following()
{
    return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
}

public function followers()
{
    return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
}
}
