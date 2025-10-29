<?php

namespace App\Http\Controllers;

use App\Models\User;

class FollowController extends Controller
{
    public function follow(User $user)
    {
        $me = auth()->user();

        if ($me->id === $user->id) {
            return response()->json(['error' => 'You cannot follow yourself'], 400);
        }

        $me->following()->syncWithoutDetaching([$user->id]);

        return response()->json(['status' => 'followed']);
    }

    public function unfollow(User $user)
    {
        auth()->user()->following()->detach($user->id);

        return response()->json(['status' => 'unfollowed']);
    }
}
