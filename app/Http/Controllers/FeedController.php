<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $posts = Post::whereIn('user_id', function($q) use ($userId) {
                $q->select('followed_id')
                  ->from('follows')
                  ->where('follower_id', $userId);
            })
            ->orWhere('user_id', $userId)
            ->with(['user', 'media', 'comments'])
            ->latest()
            ->paginate(10);

        return view('feed.index', compact('posts'));
    }
}
