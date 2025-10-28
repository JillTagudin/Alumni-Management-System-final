<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);
    
        // Check if credentials are valid
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            // Log failed login attempt
            ActivityLog::log(
                'login_failed',
                'Failed login attempt',
                null,
                ['email' => $request->email]
            );

            // Security alert logged in ActivityLog above
        
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }
        
        // Check if user account is approved
        if ($user->approval_status === 'pending') {
            ActivityLog::log(
                'login_blocked_pending_approval',
                'Login blocked - account pending approval',
                $user->id,
                ['email' => $request->email, 'approval_status' => 'pending']
            );
            
            return back()->withErrors([
                'email' => 'Your account is pending approval. Please wait for an administrator to approve your account.',
            ])->onlyInput('email');
        }
        
        if ($user->approval_status === 'denied') {
            ActivityLog::log(
                'login_blocked_denied',
                'Login blocked - account denied',
                $user->id,
                ['email' => $request->email, 'approval_status' => 'denied']
            );
            
            return back()->withErrors([
                'email' => 'Your account has been denied. Please contact an administrator for more information.',
            ])->onlyInput('email');
        }
        
        // Generate and send 2FA code
        $code = $user->generateTwoFactorCode();
        
        try {
            \Illuminate\Support\Facades\Mail::raw(
                "Your verification code is: {$code}\n\nThis code will expire in 10 minutes.\n\nIf you did not request this code, please ignore this email.",
                function ($message) use ($user) {
                    $message->to($user->email)
                           ->subject('Your Two-Factor Authentication Code');
                }
            );
            
            // Log successful credential verification and 2FA code sent
            ActivityLog::log(
                '2fa_code_sent',
                'Credentials verified, 2FA code sent',
                $user->id,
                ['email' => $request->email]
            );
            
            // Store user ID in session for 2FA verification
            session(['2fa_user_id' => $user->id]);
            
            return redirect()->route('two-factor.challenge')
                           ->with('message', 'A verification code has been sent to your email address.');
            
        } catch (\Exception $e) {
            // Log the email error for debugging
            \Illuminate\Support\Facades\Log::error('Failed to send 2FA email: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage()
            ]);
            
            // For now, bypass email sending and proceed with 2FA
            ActivityLog::log(
                '2fa_email_failed',
                'Email sending failed, proceeding with 2FA anyway',
                $user->id,
                ['email' => $request->email, 'error' => $e->getMessage()]
            );
            
            session(['2fa_user_id' => $user->id]);
            
            return redirect()->route('two-factor.challenge')
                           ->with('message', 'Please enter your verification code. (Email sending temporarily unavailable)');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Log logout before destroying session
        ActivityLog::log(
            'logout',
            'User logged out',
            auth()->id()
        );

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
