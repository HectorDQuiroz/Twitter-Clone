<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function like(Post $post)
    {
        $user = Auth::user();

        if ($user->hasLikedPost($post)) {
            return response()->json(['message' => 'Ya has dado like a este post.'], 400);
        }

        $like = new Like;
        $like->user_id = $user->id;
        $like->post_id = $post->id;
        $like->save();

        return response()->json(['message' => 'Like agregado correctamente.', 'likes_count' => $post->likesCount()]);
    }

    public function unlike(Post $post)
    {
        $user = Auth::user();

        if (!$user->hasLikedPost($post)) {
            return response()->json(['message' => 'No has dado like a este post.'], 400);
        }

        $like = $user->likes()->where('post_id', $post->id)->first();
        $like->delete();

        return response()->json(['message' => 'Like eliminado correctamente.', 'likes_count' => $post->likesCount()]);
    }
}