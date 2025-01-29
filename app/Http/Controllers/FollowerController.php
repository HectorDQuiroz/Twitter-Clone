<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowerController extends Controller
{
    public function follow(User $user)
    {
        $follower = Auth::user();
        if (!$follower->isFollowing($user)) {
            $follower->following()->attach($user);
            return response()->json(['message' => 'Ahora sigues a ' . $user->name, 'success' => true]);
        }
        return response()->json(['message' => 'Ya sigues a este usuario.', 'success' => false]);
    }
    
    public function unfollow(User $user)
    {
        $follower = Auth::user();
        if ($follower->isFollowing($user)) {
            $follower->following()->detach($user);
            return response()->json(['message' => 'Has dejado de seguir a ' . $user->name, 'success' => true]);
        }
        return response()->json(['message' => 'No sigues a este usuario.', 'success' => false]);
    }
}
