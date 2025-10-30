<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['store', 'like']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,webm|max:20480',
        ]);

        $post = auth()->user()->posts()->create([
            'content' => $request->input('content'),
        ]);

        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('posts', 'public');
            $post->update(['media' => $path]);
        }

        return redirect()->back()->with('status', 'Post published.');
    }

    public function like(Post $post)
    {
        $user = auth()->user();

        if (!$user) return redirect()->route('login');

        $existing = $post->likes()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            $message = 'Unliked';
        } else {
            $post->likes()->create(['user_id' => $user->id]);
            $message = 'Liked';
        }

        return redirect()->back()->with('status', $message);
    }
}
