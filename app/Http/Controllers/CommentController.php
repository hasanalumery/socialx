<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a new comment on a post.
     * Returns JSON for AJAX or redirects for normal submission.
     */
    public function store(Request $request, Post $post)
    {
        $data = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $data['body'],
        ]);

        $comment->load('user'); // ensure user relationship for response

        // If AJAX/JSON requested, respond with structured JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'ok',
                'comment' => [
                    'id' => $comment->id,
                    'user_id' => $comment->user->id,
                    'user_name' => $comment->user->name,
                    'body' => $comment->body,
                    'created_at' => $comment->created_at->toDateTimeString(),
                ],
                'comments_count' => $post->comments()->count(),
            ]);
        }

        return back()->with('status', 'Comment added.');
    }

    /**
     * Toggle like/unlike on a comment.
     * Returns JSON with updated like status and count.
     */
    public function like(Comment $comment)
    {
        $user = auth()->user();

        // Toggle like: if exists, remove; if not, create
        if ($comment->likes()->where('user_id', $user->id)->exists()) {
            $comment->likes()->where('user_id', $user->id)->delete();
            $status = 'unliked';
        } else {
            $comment->likes()->create(['user_id' => $user->id]);
            $status = 'liked';
        }

        return response()->json([
            'status' => $status,
            'likes_count' => $comment->likes()->count(),
        ]);
    }
}
