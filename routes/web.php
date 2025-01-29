<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowerController;


Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/posts/search', [PostController::class, 'search'])->name('posts.search');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
Route::put('/profile', [UserController::class, 'update'])->name('profile.update');

Route::resource('posts', PostController::class)->middleware('auth');
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
Route::post('/posts/{post}/like', [LikeController::class, 'like'])->name('posts.like')->middleware('auth');
Route::delete('/posts/{post}/like', [LikeController::class, 'unlike'])->name('posts.unlike')->middleware('auth');

Route::post('/users/{user}/follow', [FollowerController::class, 'follow'])->name('users.follow');
Route::delete('/users/{user}/unfollow', [FollowerController::class, 'unfollow'])->name('users.unfollow');

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
