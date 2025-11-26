<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->input('q'));

        // -------------------------
        // 1. USERS (alleen bij search)
        // -------------------------
        $users = collect();

        if ($query !== '') {
            $users = User::where('name', 'like', "%{$query}%")
                ->take(15)
                ->get(['id', 'name', 'bio', 'profile_picture']);
        }

        // -------------------------
        // 2. POST CONTENT COLUMN DETECTIE
        // -------------------------
        $possibleTextCols = ['body', 'caption', 'content', 'text', 'description'];
        $textColumn = null;

        foreach ($possibleTextCols as $col) {
            if (Schema::hasColumn('posts', $col)) {
                $textColumn = $col;
                break;
            }
        }

        // -------------------------
        // 3. POSTS QUERY
        // -------------------------
        $postsQuery = Post::with(['user', 'likes', 'comments.user', 'comments.likes'])
            ->withCount(['likes', 'comments'])
            ->latest();

        if ($query !== '') {
            $matchedUser = User::where('name', 'like', "%{$query}%")->first();

            if ($matchedUser) {
                $postsQuery->where('user_id', $matchedUser->id);
            } elseif ($textColumn) {
                $postsQuery->where($textColumn, 'like', "%{$query}%");
            }
        }

        $posts = $postsQuery->paginate(12)->withQueryString();

        return view('explore', compact('posts', 'users', 'query'));
    }
}
