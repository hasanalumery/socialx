<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a comment for a given post.
     * Returns JSON for AJAX requests or redirects back for normal requests.
     */
    public function store(Request $request, Post $post)
    {
        $data = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = $post->comments()->create([
            'user_id' => Auth::id(),
            'body'    => $data['body'],
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'comment' => [
                    'id' => $comment->id,
                    'user_name' => $comment->user->name,
                    'body' => $comment->body,
                    'created_at' => $comment->created_at->diffForHumans(),
                ],
            ]);
        }

        return back()->with('status', 'Comment added.');
    }

    public function like(Comment $comment)
{
    $user = auth()->user();

    $comment->likes()->toggle($user->id);

    return response()->json([
        'liked' => $comment->likes()->where('user_id', $user->id)->exists(),
        'likes_count' => $comment->likes()->count(),
    ]);
}

}