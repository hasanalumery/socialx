<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    public function follow(User $user)
    {
        $me = Auth::user();

        // Prevent following yourself
        if ($me->id === $user->id) {
            return back()->with('error', 'You cannot follow yourself.');
        }

        // Check if already following (correct column: following_id)
        if (!$me->following()->where('following_id', $user->id)->exists()) {
            $me->following()->attach($user->id);  // attaches as following_id
        }

        return back();
    }

    public function unfollow(User $user)
    {
        $me = Auth::user();

        // Detach the relationship via following_id
        $me->following()->detach($user->id);

        return back();
    }
}
