<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;

class LikeController extends Controller
{
    public function toggle($postId)
    {
        $post = Post::findOrFail($postId);

        $like = Like::where('post_id', $postId)
            ->where('user_id', auth()->id())
            ->first();

        if ($like) {
            $like->delete(); // unlike
        } else {
            Like::create([
                'post_id' => $postId,
                'user_id' => auth()->id(),
            ]);
        }

        return back();
    }
}
