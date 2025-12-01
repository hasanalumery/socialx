<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['store', 'update', 'destroy', 'like']);
    }

    public function index()
    {
        $posts = Post::with(['user', 'likes', 'comments.user'])->latest()->paginate(12);
        return view('posts.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string|max:1000',
            'media'   => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,webm|max:20480',
        ]);

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/posts'), $filename);
            $data['media'] = 'uploads/posts/'.$filename;
        }

        $request->user()->posts()->create($data);

        return redirect()->back()->with('status', 'Post published.');
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $data = $request->validate([
            'content' => 'required|string|max:1000',
            'media'   => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,webm|max:20480',
        ]);

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/posts'), $filename);
            $data['media'] = 'uploads/posts/'.$filename;
        }

        $post->update($data);

        return redirect()->back()->with('status', 'Post updated.');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return redirect()->back()->with('status', 'Post deleted.');
    }

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
