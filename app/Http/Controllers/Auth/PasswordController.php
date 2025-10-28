<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);
        
        // Log password update
        ActivityLog::log(
            'password_update',
            'User updated their password',
            auth()->id()
        );
        
        // Send password change notification email
        try {
            Mail::send('emails.password-changed-notification', [
                'userName' => $user->name,
                'changeTime' => now()->format('F j, Y \\a\\t g:i A'),
                'ipAddress' => $request->ip(),
                'userAgent' => $request->userAgent(),
                'loginUrl' => route('welcome')
            ], function ($message) use ($user) {
                $message->to($user->email)
                       ->subject('Security Alert: Password Changed - Alumni Management System');
            });
        } catch (\Exception $e) {
            // Log email error but don't fail the password update
            \Illuminate\Support\Facades\Log::error('Failed to send password change notification email: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }

        // Log out the user after password change for security
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome')->with('status', 'Password updated successfully! Please log in with your new password.');
    }
}
