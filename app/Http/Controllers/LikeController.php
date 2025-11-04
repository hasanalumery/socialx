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

    // Unlike a post
    public function destroy(Post $post)
    {
        $user = Auth::user();

        $post->likes()->where('user_id', $user->id)->delete();

        return back()->with('status', 'Like removed.');
    }
}
