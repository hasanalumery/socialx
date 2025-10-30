<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    // Apply auth middleware to store and delete
    public function __construct()
    {
        $this->middleware('auth')->only(['store', 'destroy', 'like']);
    }

    /**
     * Store a new post
     */
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,webm|max:20480',
        ]);

        $user = $request->user();

        $post = $user->posts()->create([
            'content' => $request->input('content'),
        ]);

        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('posts', 'public');
            $post->update(['media' => $path]);
        }

        return redirect()->back()->with('status', 'Post published.');
    }

    /**
     * Show a single post
     */
    public function show(Post $post)
    {
        $post->load(['user', 'likes', 'comments.user']);
        return view('posts.show', compact('post'));
    }

    /**
     * Delete a post
     */
    public function destroy(Post $post)
    {
        // Simple owner check
        if ($post->user_id !== auth()->id()) {
            abort(403);
        }

        $post->delete();
        return redirect()->back()->with('status', 'Post deleted.');
    }

    /**
     * Toggle like/unlike
     */
    public function like(Post $post)
    {
        $user = auth()->user();
        if (!$user) return redirect()->route('login');

        $existing = $post->likes()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
        } else {
            $post->likes()->create(['user_id' => $user->id]);
        }

        return redirect()->back();
    }
}
