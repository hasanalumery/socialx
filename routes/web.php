<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    FeedController,
    PostController,
    ProfileController,
    LikeController,
    FollowController,
    CommentController,
    ExploreController,
};

// Public pages
Route::get('/', [FeedController::class, 'index'])->name('home');
Route::get('/explore', [ExploreController::class, 'index'])->name('explore'); // points to ExploreController

// Auth routes (Laravel Breeze)
require __DIR__ . '/auth.php';

// Protected routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // My profile shortcut
    Route::get('/profile', fn() => redirect()->route('profile.show', auth()->user()))
        ->name('profile.self');

    // Profile management
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');  
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Posts CRUD (except create/edit pages)
    Route::resource('posts', PostController::class)->except(['create', 'edit']);

    // Post like/unlike (AJAX)
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('post.like');

    // Comment store (AJAX)
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comment.store');

    // Comment like/unlike (AJAX)
    Route::post('/comments/{comment}/like', [CommentController::class, 'like'])->name('comments.like');

    // Follows
    Route::post('/users/{user}/follow', [FollowController::class, 'follow'])->name('user.follow');
    Route::delete('/users/{user}/follow', [FollowController::class, 'unfollow'])->name('user.unfollow');

    // Short follow/unfollow routes
    Route::post('/follow/{user}', [FollowController::class, 'follow'])->name('follow');
    Route::delete('/follow/{user}', [FollowController::class, 'unfollow'])->name('unfollow');

});

// Public profile
Route::get('/users/{user}', [ProfileController::class, 'show'])->name('profile.show');
