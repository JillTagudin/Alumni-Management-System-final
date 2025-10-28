<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $oldData = $user->toArray();
        
        // Validate the request including profile picture and social media fields
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'facebook_profile' => ['nullable', 'url', 'max:255'],
            'linkedin_profile' => ['nullable', 'url', 'max:255'],
            'twitter_profile' => ['nullable', 'url', 'max:255'],
            'instagram_profile' => ['nullable', 'url', 'max:255'],
        ]);
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            
            // Store new profile picture
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $validated['profile_picture'] = $profilePicturePath;
        }
        
        $user->fill($validated);

        if ($user->isDirty('email') && $user->role !== 'Alumni') {
            $user->email_verified_at = null;
        }

        $user->save();

        // Log profile update
        ActivityLog::log(
            'profile_updated',
            'User updated their profile',
            auth()->id(),
            ['changes' => $user->getChanges()]
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Log account deletion before deleting
        ActivityLog::log(
            'account_deletion',
            'User deleted their account',
            $user->id,
            ['email' => $user->email, 'name' => $user->name]
        );

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
