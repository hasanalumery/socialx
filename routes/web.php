<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    FeedController,
    PostController,
    ProfileController,
    LikeController,
    FollowController,
    CommentController
};

// Public routes
Route::get('/', [FeedController::class, 'index'])->name('home');
Route::get('/explore', [FeedController::class, 'explore'])->name('explore');
Route::get('/users/{user:username}', [ProfileController::class, 'show'])->name('profile.show');

// Auth routes
require __DIR__.'/auth.php';

// Protected routes
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Posts
    Route::resource('posts', PostController::class)->except(['create', 'edit']);
    Route::post('/posts/{post}/like', [LikeController::class, 'store'])->name('posts.like');
    Route::delete('/posts/{post}/like', [LikeController::class, 'destroy'])->name('posts.unlike');

    // Follows
    Route::post('/users/{user}/follow', [FollowController::class, 'follow'])->name('user.follow');
    Route::delete('/users/{user}/follow', [FollowController::class, 'unfollow'])->name('user.unfollow');

    // Comments
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

    // dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    Route::post('/posts/{post}/like', [LikeController::class, 'store'])->name('posts.like');
Route::delete('/posts/{post}/like', [LikeController::class, 'destroy'])->name('posts.unlike');
Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');


});
