<?php

namespace App\Http\Controllers;

use App\Models\Post;

class LikeController extends Controller
{
    public function like(Post $post)
    {
        $user = auth()->user();

        if ($post->user_id === $user->id) {
            return response()->json(['error' => 'You cannot like your own post'], 400);
        }

        if (! $post->likes()->where('user_id', $user->id)->exists()) {
            $post->likes()->create(['user_id' => $user->id]);
        }

        return response()->json(['status' => 'liked']);
    }

    public function unlike(Post $post)
    {
        $user = auth()->user();

        $post->likes()->where('user_id', $user->id)->delete();

        return response()->json(['status' => 'unliked']);
    }

    public function toggle(Post $post)
    {
        $user = auth()->user();

        $existing = $post->likes()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['status' => 'unliked']);
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            return response()->json(['status' => 'liked']);
        }
    }
}
