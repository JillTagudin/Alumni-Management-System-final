<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Verify the user's password via AJAX.
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();
        
        if (! Auth::guard('web')->validate([
            'email' => $user->email,
            'password' => $request->password,
        ])) {
            // Log failed password verification attempt
            ActivityLog::log(
                'alumni_password_verification_failed',
                'Failed password verification attempt for alumni records access',
                $user->id,
                [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'attempted_at' => now()->format('Y-m-d H:i:s')
                ]
            );
            
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password. Please try again.'
            ], 422);
        }

        // Log successful password verification
        ActivityLog::log(
            'alumni_password_verification_success',
            'Successful password verification for alumni records access',
            $user->id,
            [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'verified_at' => now()->format('Y-m-d H:i:s')
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Password verified successfully.'
        ]);
    }
}
