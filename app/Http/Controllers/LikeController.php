<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Like a post
    public function store(Post $post)
    {
        $user = Auth::user();

        if ($post->user_id === $user->id) {
            return back()->withErrors(['You cannot like your own post.']);
        }

        $post->likes()->firstOrCreate(['user_id' => $user->id]);

        return back()->with('status', 'Post liked.');
    }

    public function toggle(Post $post)
{
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $existing = $post->likes()->where('user_id', $user->id)->first();

    if ($existing) {
        $existing->delete();
        $status = 'unliked';
    } else {
        $post->likes()->create(['user_id' => $user->id]);
        $status = 'liked';
    }

    // return JSON including updated count
    if (request()->wantsJson() || request()->ajax()) {
        return response()->json([
            'status' => $status,
            'likes_count' => $post->likes()->count(),
        ]);
    }

    return redirect()->back();
}

    // Unlike a post
    public function destroy(Post $post)
    {
        $user = Auth::user();

        $post->likes()->where('user_id', $user->id)->delete();

        return back()->with('status', 'Like removed.');
    }
}
