<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;

// 👇 Public Routes
Route::get('/', [FeedController::class, 'index'])->name('home');
Route::get('/explore', [FeedController::class, 'explore'])->name('explore');

// 👇 Public profile viewing
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

// 👇 Authentication routes (Laravel Breeze/Jetstream)
require __DIR__ . '/auth.php';

// 👇 Protected Routes (require login)
Route::middleware('auth')->group(function () {

    // ---- User Profile ----
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ---- Dashboard ----
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // ---- Posts ----
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // ---- Likes ----
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like');

    // ---- Follows ----
    Route::post('/users/{user}/follow', [FollowController::class, 'follow'])->name('user.follow');
    Route::delete('/users/{user}/follow', [FollowController::class, 'unfollow'])->name('user.unfollow');

    // ---- Comments ----
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
});
