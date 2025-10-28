<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    /**
     * Show the 2FA verification form
     */
    public function show()
    {
        if (!session('2fa_user_id')) {
            return redirect()->route('login');
        }
        
        return view('auth.two-factor-challenge');
    }

    /**
     * Send 2FA code to user's email
     */
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'The provided email address does not exist in our records.',
            ]);
        }

        $code = $user->generateTwoFactorCode();
        
        // Send email with 2FA code
        try {
            Mail::raw(
                "Your verification code is: {$code}\n\nThis code will expire in 10 minutes.\n\nIf you did not request this code, please ignore this email.",
                function ($message) use ($user) {
                    $message->to($user->email)
                           ->subject('Your Two-Factor Authentication Code');
                }
            );
            
            // Log the 2FA code generation
            ActivityLog::log(
                '2fa_code_sent',
                'Two-factor authentication code sent',
                $user->id,
                ['email' => $user->email]
            );
            
            session(['2fa_user_id' => $user->id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your email address.'
            ]);
            
        } catch (\Exception $e) {
            // Log the email error for debugging
            \Illuminate\Support\Facades\Log::error('Failed to send 2FA email in sendCode: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            
            // Log the failed email attempt
            ActivityLog::log(
                '2fa_email_failed',
                'Email sending failed during code generation',
                $user->id,
                ['email' => $user->email, 'error' => $e->getMessage()]
            );
            
            session(['2fa_user_id' => $user->id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Verification code generated. (Email sending temporarily unavailable)'
            ]);
        }
    }

    /**
     * Verify the 2FA code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('2fa_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->verifyTwoFactorCode($request->code)) {
            throw ValidationException::withMessages([
                'code' => 'The provided verification code is invalid or has expired.',
            ]);
        }

        // Clear the 2FA code and session
        $user->clearTwoFactorCode();
        session()->forget('2fa_user_id');
        
        // Log successful 2FA verification
        ActivityLog::log(
            '2fa_verified',
            'Two-factor authentication verified successfully',
            $user->id
        );

        // Log the user in
        Auth::login($user);
        $request->session()->regenerate();
        
        // Log successful login after 2FA
        ActivityLog::log(
            'login_success',
            'User logged in successfully after 2FA verification',
            $user->id,
            ['email' => $user->email, 'role' => $user->role]
        );

        // Redirect based on user role
        if ($user->hasAdminPrivileges()) {
            return redirect()->intended('dashboard');
        } elseif ($user->isHR()) {
            return redirect()->intended('hr/dashboard');
        }
        
        return redirect()->intended('user/dashboard');
    }

    /**
     * Resend 2FA code
     */
    public function resend(Request $request)
    {
        $userId = session('2fa_user_id');
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please start the login process again.'
            ], 400);
        }

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $code = $user->generateTwoFactorCode();
        
        try {
            Mail::raw(
                "Your new verification code is: {$code}\n\nThis code will expire in 10 minutes.\n\nIf you did not request this code, please ignore this email.",
                function ($message) use ($user) {
                    $message->to($user->email)
                           ->subject('Your Two-Factor Authentication Code (Resent)');
                }
            );
            
            ActivityLog::log(
                '2fa_code_resent',
                'Two-factor authentication code resent',
                $user->id,
                ['email' => $user->email]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'New verification code sent to your email address.'
            ]);
            
        } catch (\Exception $e) {
            // Log the email error for debugging
            \Illuminate\Support\Facades\Log::error('Failed to resend 2FA email: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            
            ActivityLog::log(
                '2fa_email_failed',
                'Email sending failed during code resend',
                $user->id,
                ['email' => $user->email, 'error' => $e->getMessage()]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'New verification code generated. (Email sending temporarily unavailable)'
            ]);
        }
    }
}