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

        // Base query for posts (all posts)
        $postsQuery = Post::with(['user', 'media', 'comments', 'likes'])->latest();

        // If logged in, filter posts to followed users + self
        if ($userId) {
            $followedIds = DB::table('follows')
                ->where('follower_id', $userId)
                ->pluck('followed_id')
                ->toArray();

            // If the user follows anyone, filter posts
            if (!empty($followedIds)) {
                $postsQuery->whereIn('user_id', $followedIds)
                           ->orWhere('user_id', $userId);
            }
            // else fallback: show all posts (no filter)
        }

        // Paginate posts (unified)
        $posts = $postsQuery->paginate(10);

        // Render home.blade.php with posts
        return view('home', compact('posts'));
    }
}
