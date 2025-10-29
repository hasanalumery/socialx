<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Like;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['store','like']);
    }

    // store a new post
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post = auth()->user()->posts()->create([
            'content' => $request->input('content'),
        ]);

        return redirect()->back()->with('status', 'Post published.');
    }

    // toggle like/unlike
    public function like(Post $post)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

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
