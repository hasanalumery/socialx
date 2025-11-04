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
     * Attributes that are mass assignable.
     *
     * Keep this explicit to avoid mass-assignment vulnerabilities.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'content',
        'media',
    ];

    /**
     * Casts for date handling.
     *
     * @var array<string,string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Post owner.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Likes on this post.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Comments on this post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Check if this post is liked by the given user id.
     *
     * Uses the loaded relationship if present to avoid extra queries,
     * otherwise falls back to an efficient existence check.
     */
    public function isLikedBy(int $userId): bool
    {
        if ($this->relationLoaded('likes')) {
            return $this->likes->contains('user_id', $userId);
        }

        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Toggle like for a user. Returns true if liked, false if unliked.
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
