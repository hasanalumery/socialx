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

// Auth routes (Breeze)
require __DIR__ . '/auth.php';

// Protected routes (require auth)
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // My profile
    Route::get('/profile', function () {
        return redirect()->route('profile.show', auth()->user());
    })->name('profile.self');

    // Edit/update/delete profile
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');  
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Posts
    Route::resource('posts', PostController::class)->except(['create', 'edit']);

    // Post like toggle
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like');

    // Comments - store
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

    // **ADD HERE: Comment like toggle**
    Route::post('/comments/{comment}/like', [CommentController::class, 'like'])->name('comments.like');

    // Follows
    Route::post('/users/{user}/follow', [FollowController::class, 'follow'])->name('user.follow');
    Route::delete('/users/{user}/follow', [FollowController::class, 'unfollow'])->name('user.unfollow');
});

// Public profile view
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
