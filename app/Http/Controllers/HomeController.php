<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post; // assuming you already have a Post model

class HomeController extends Controller
{
    public function index()
    {
        // Fetch all posts, newest first
        $posts = Post::with('user')->latest()->get();

        // Return them to the view
        return view('home', compact('posts'));
    }
}
