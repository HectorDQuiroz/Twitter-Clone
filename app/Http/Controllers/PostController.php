<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

    public function index()
    {
        $posts = Post::with('user', 'likes')->latest()->paginate(10);
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|max:280',
        ]);
    
        $post = new Post;
        $post->content = $request->content;
        $post->user_id = auth()->id();
        $post->save();
    
        return response()->json([
            'id' => $post->id,
            'content' => $post->content,
            'user' => [
                'id' => $post->user->id,
                'name' => $post->user->name,
                'username' => $post->user->username,
            ],
            'created_at' => $post->created_at->diffForHumans(),
        ]);
    }

    public function show(Post $post)
    {
        $post->load('user', 'comments.user', 'likes');
        if (request()->ajax()) {
            return view('posts.show', compact('post'))->render();
        }
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        if (auth()->user()->id !== $post->user_id) {
            return redirect()->route('posts.index')->with('error', 'No tienes permiso para editar este post.');
        }
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        if (auth()->user()->id !== $post->user_id) {
            return response()->json(['message' => 'No tienes permiso para editar este post.'], 403);
        }
        $request->validate([
            'content' => 'required|max:280',
        ]);
    
        $post->update([
            'content' => $request->content,
        ]);
    
        return response()->json(['message' => 'Post actualizado exitosamente!', 'post' => $post]);
    }

    public function destroy(Post $post)
    {
        if (auth()->user()->id !== $post->user_id) {
            return response()->json(['message' => 'No tienes permiso para eliminar este post.'], 403);
        }
    
        $post->delete();
    
        return response()->json(['message' => 'Post eliminado exitosamente!']);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        if ($query) {
            $posts = Post::with('user', 'likes')
                ->where('content', 'like', "%{$query}%")
                ->latest()
                ->paginate(10);
        } else {
            $posts = Post::with('user', 'likes')->latest()->paginate(10);
        }


        if ($request->ajax()) {
            return view('posts.posts', compact('posts'))->render();
        }

        return view('posts.index', compact('posts'));
    }
}