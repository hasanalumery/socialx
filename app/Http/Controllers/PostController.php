<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function store(Request $request)
    {
        // 1️⃣ Validate input first
        $request->validate([
            'content' => 'required|string|max:255',
            'image'   => 'nullable|image|max:2048', // optional image validation
        ]);

        // 2️⃣ Create the post
        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        // 3️⃣ If an image is uploaded, store it and attach it to the post
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('posts', 'public');
            $post->media()->create([
                'path' => 'storage/' . $path,
            ]);
        }

        // 4️⃣ Redirect back
        return redirect()->route('home');
    }
}
