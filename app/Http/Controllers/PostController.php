<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class PostController extends Controller
{
    public function __construct()
    {
        // require auth for store/update/destroy/like
        $this->middleware('auth')->only(['store', 'update', 'destroy', 'like']);
    }

    /**
     * Display a listing of posts (public/index).
     */
    public function index()
    {
        // eager load relations to avoid N+1
        $posts = Post::with(['user', 'likes', 'comments.user'])->latest()->paginate(12);
        return view('posts.index', compact('posts'));
    }

    /**
     * Store a new post — validated, file-safe, mass-assignment safe.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string|max:1000',
            'media'   => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,webm|max:20480',
        ]);

        if ($request->hasFile('media')) {
            $data['media'] = $request->file('media')->store('posts', 'public');
        }

        // create via relationship, user_id filled automatically
        $post = $request->user()->posts()->create($data);

        return redirect()->back()->with('status', 'Post published.');
    }

    /**
     * Show a single post.
     */
    public function show(Post $post)
    {
        $post->load(['user', 'likes', 'comments.user']);
        return view('posts.show', compact('post'));
    }

    /**
     * Update a post (simple owner check).
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $data = $request->validate([
            'content' => 'required|string|max:1000',
            'media'   => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,webm|max:20480',
        ]);

        if ($request->hasFile('media')) {
            $data['media'] = $request->file('media')->store('posts', 'public');
        }

        $post->update($data);

        return redirect()->back()->with('status', 'Post updated.');
    }

    /**
     * Delete a post (owner check).
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return redirect()->back()->with('status', 'Post deleted.');
    }

    /**
     * Like/unlike (toggle) handled by LikeController if you prefer separation.
     * This is a simple inline toggle if you keep it here.
     */
    public function like(Post $post)
    {
        $user = Auth::user();
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
