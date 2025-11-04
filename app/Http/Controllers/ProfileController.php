<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show a user's public profile.
     */
    public function show(User $user): View
    {
        // Eager-load related data to minimize queries
        $posts = $user->posts()
            ->with(['likes', 'comments.user'])
            ->latest()
            ->paginate(10);

        return view('profile.show', compact('user', 'posts'));
    }

    /**
     * Show the authenticated user's edit profile form.
     */
    public function edit(): View
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Validation: name required, bio optional, profile picture optional
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old picture if it exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Store new one
            $validated['profile_picture'] = $request
                ->file('profile_picture')
                ->store('profile_pictures', 'public');
        }

        // Update user fields
        $user->update($validated);

        // Redirect to public profile view
        return redirect()
            ->route('profile.show', $user)
            ->with('status', 'Profile updated successfully!');
    }

    /**
     * Delete the authenticated user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();

        // Clean up profile image
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('status', 'Your account has been deleted.');
    }
}
