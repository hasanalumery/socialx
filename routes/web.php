<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\CommentController;

// 👇 Public feed / homepage
Route::get('/', [FeedController::class, 'index'])->name('home');

// 👇 Authentication routes (from Laravel Breeze or Jetstream)
require __DIR__ . '/auth.php';

// 👇 Protected routes (only accessible when logged in)
Route::middleware('auth')->group(function () {

    // ---- DASHBOARD ----
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // ---- POSTS ----
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    // posts
Route::middleware('auth')->group(function () {
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::post('/posts/{post}/like', [PostController::class, 'like'])->name('posts.like');
});

    // ---- LIKES ----
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like');

    // ---- FOLLOWS ----
    Route::post('/users/{user}/follow', [FollowController::class, 'follow'])->name('user.follow');
    Route::delete('/users/{user}/follow', [FollowController::class, 'unfollow'])->name('user.unfollow');

    // ---- COMMENTS ----
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
});
