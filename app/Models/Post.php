<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use App\Models\User;
use App\Models\Like;
use App\Models\Comment;

class Post extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'content',
        'media',
    ];

    /**
     * Cast timestamps
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Post belongs to a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Post has many likes
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Relationship: Post has many comments (newest first)
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    /**
     * Check if this post is liked by a given user or user ID
     * Accepts: User model, numeric ID, or null
     */
    public function isLikedBy($user): bool
    {
        if (!$user) return false; // Not logged in

        $userId = $user instanceof User ? $user->id : $user;

        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Efficient check if post is liked by user ID when likes are loaded
     */
    public function isLikedByUser(int $userId): bool
    {
        return $this->relationLoaded('likes')
            ? $this->likes->contains('user_id', $userId)
            : $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Toggle like for a user
     * Returns: true = liked, false = unliked
     */
    public function toggleLikeBy(User $user): bool
    {
        $existing = $this->likes()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            return false;
        }

        $this->likes()->create(['user_id' => $user->id]);

        return true;
    }
}
