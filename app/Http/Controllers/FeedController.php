<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedController extends Controller
{
    // Home feed
    public function index(Request $request)
    {
        $userId = auth()->id();

        $postsQuery = Post::with(['user', 'likes', 'comments.user'])
                          ->latest();

        if ($userId) {
            // Get IDs of users the current user follows
            $followedIds = DB::table('follows')
                             ->where('follower_id', $userId)
                             ->pluck('following_id')
                             ->toArray();

            if (!empty($followedIds)) {
                // Include posts from followed users + self
                $postsQuery->whereIn('user_id', $followedIds)
                           ->orWhere('user_id', $userId);
            } else {
                // Only self posts if not following anyone
                $postsQuery->where('user_id', $userId);
            }
        }

        // Paginate posts for the feed
        $posts = $postsQuery->paginate(10);

        return view('home', compact('posts'));
    }

    // Explore page
    public function explore()
    {
        $posts = Post::with(['user', 'likes', 'comments.user'])
                     ->withCount(['likes','comments'])
                     ->latest()
                     ->paginate(12);

        return view('explore', compact('posts'));
    }
}
