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

// ------------------------
// Public Routes
// ------------------------

// Homepage & Explore
Route::get('/', [FeedController::class, 'index'])->name('home');
Route::get('/explore', [FeedController::class, 'explore'])->name('explore');

// Authentication routes (Breeze/Jetstream)
require __DIR__ . '/auth.php';

// ------------------------
// Protected Routes (require login)
// ------------------------
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // My profile routes
    Route::get('/profile', function() {
        return redirect()->route('profile.show', auth()->user());
    })->name('profile.self');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
   Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Posts & interactions
    Route::resource('posts', PostController::class)->except(['create', 'edit']); // store, show, destroy
    Route::post('/posts/{post}/like', [LikeController::class, 'toggle'])->name('posts.like');
    Route::delete('/posts/{post}/like', [LikeController::class, 'destroy'])->name('posts.unlike');
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');

    // Follows
    Route::post('/users/{user}/follow', [FollowController::class, 'follow'])->name('user.follow');
    Route::delete('/users/{user}/follow', [FollowController::class, 'unfollow'])->name('user.unfollow');
});

// ------------------------
// Public Profile Viewing
// ------------------------
// IMPORTANT: place this AFTER /profile and /profile/edit routes to prevent conflicts
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
