<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(User $user)
    {
        $posts = $user->posts()->with('user', 'likes')->latest()->paginate(10);

        return view('users.show', compact('user', 'posts'));
    }

    public function edit()
    {
        $user = Auth::user(); 
        return view('users.edit', compact('user'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user(); 
    
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);
    
        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ]);
    
        return redirect()->route('users.show', $user)->with('success', 'Perfil actualizado correctamente.');
    } 
}