<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        Post::create([
            'user_id' => Auth::id(),
           'content' => $request->input('content'),
        ]);

        return redirect()->route('home');
    }
}
