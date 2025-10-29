<?php
namespace App\Http\Controllers;

use App\Models\Post;

class HomeController extends Controller
{
    public function index()
    {
        // eager load user + likes; paginate for production readiness
        $posts = Post::with(['user','likes'])->latest()->paginate(10);
        return view('home', compact('posts'));
    }
}
