<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Toggle like/unlike on a post.
     * Returns JSON if the request is AJAX.
     */
    public function toggle(Request $request, Post $post)
    {
        $user = $request->user();

        // Check if user already liked the post
        $existing = $post->likes()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            $status = 'unliked';
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $status = 'liked';
        }

        $likesCount = $post->likes()->count();

        // Return JSON for AJAX requests
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => $status,
                'likes_count' => $likesCount,
            ]);
        }

        // Fallback for normal requests
        return back();
    }
}