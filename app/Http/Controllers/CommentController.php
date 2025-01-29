<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|max:280',
        ]);
    
        $comment = new Comment;
        $comment->content = $request->content;
        $comment->user_id = Auth::id();
        $comment->post_id = $post->id;
        $comment->save();

        return response()->json([
            'message' => 'Comentario añadido correctamente.',
            'comment' => [
                'content' => $comment->content,
                'created_at' => $comment->created_at->diffForHumans(),
            ],
            'user' => [
                'name' => Auth::user()->name,
            ]
        ]);
    }
}