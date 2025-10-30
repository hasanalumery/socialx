<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        $postsQuery = Post::with(['user', 'likes', 'comments.user'])
                          ->latest();

        if ($userId) {
            $followedIds = DB::table('follows')
                             ->where('follower_id', $userId)
                             ->pluck('followed_id')
                             ->toArray();

            if (!empty($followedIds)) {
                $postsQuery->whereIn('user_id', $followedIds)
                           ->orWhere('user_id', $userId);
            }
        }

        $posts = $postsQuery->paginate(10);

        return view('home', compact('posts'));
    }

    public function explore()
    {
        $posts = Post::with(['user', 'likes', 'comments.user'])
                     ->latest()
                     ->paginate(10);

        return view('explore', compact('posts'));
    }
}
