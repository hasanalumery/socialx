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
     * Supports both full-page and AJAX workflows.
     */
    public function toggle(Request $request, Post $post)
    {
        $user = $request->user();

        // Determine if like already exists
        $existing = $post->likes()
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $status = 'unliked';
        } else {
            $post->likes()->create([
                'user_id' => $user->id
            ]);
            $status = 'liked';
        }

        // Recalculate aggregated like count
        $likesCount = $post->likes()->count();

        // Serve AJAX-specific response
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status'      => $status,
                'likes_count' => $likesCount,
            ]);
        }

        // Standard redirect fallback
        return back();
    }
}
