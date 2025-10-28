<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = $request->input('email');
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        
        // Generate verification token and URL
        $verificationToken = Str::random(64);
        $verificationUrl = route('password.reset.verify', ['token' => $verificationToken]);
        
        // Store verification data in cache for 15 minutes
        Cache::put('password_reset_verification_' . $verificationToken, [
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'created_at' => now()
        ], 900); // 15 minutes

        try {
            // Send verification email
            Mail::send('emails.password-reset-verification', [
                'email' => $email,
                'verificationUrl' => $verificationUrl,
                'requestTime' => now()->format('Y-m-d H:i:s'),
                'ipAddress' => $ipAddress,
                'userAgent' => $userAgent
            ], function ($message) use ($email) {
                $message->to($email)
                       ->subject('Password Reset Verification Required');
            });
            
            return back()->with('status', 'A verification email has been sent. Please check your email and click the verification link to proceed with password reset.');
        } catch (\Exception $e) {
            Log::error('Failed to send password reset verification email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            
            return back()->withInput($request->only('email'))
                         ->withErrors(['email' => 'Failed to send verification email. Please try again.']);
         }
     }

    /**
     * Verify the password reset request and send the actual reset link.
     */
    public function verify(Request $request, string $token): RedirectResponse
    {
        $verificationData = Cache::get('password_reset_verification_' . $token);
        
        if (!$verificationData) {
            return redirect()->route('password.request')
                           ->withErrors(['email' => 'Verification link has expired or is invalid. Please request a new password reset.']);
        }
        
        $currentIp = $request->ip();
        $currentUserAgent = $request->userAgent();
        
        // Check for IP address mismatch (security measure)
        if ($verificationData['ip_address'] !== $currentIp) {
            ActivityLog::log(
                'password_reset_ip_mismatch',
                'Password reset verification attempted from different IP',
                null,
                [
                    'email' => $verificationData['email'],
                    'original_ip' => $verificationData['ip_address'],
                    'verification_ip' => $currentIp,
                    'user_agent' => $currentUserAgent
                ]
            );
            
            return redirect()->route('password.request')
                           ->withErrors(['email' => 'Security verification failed. Please request a new password reset from the same device.']);
        }
        
        // Log successful verification
        ActivityLog::log(
            'password_reset_verified',
            'Password reset request successfully verified',
            null,
            [
                'email' => $verificationData['email'],
                'ip_address' => $currentIp,
                'user_agent' => $currentUserAgent,
                'verification_time' => now()->format('Y-m-d H:i:s')
            ]
        );
        
        // Remove verification token from cache
        Cache::forget('password_reset_verification_' . $token);
        
        // Now send the actual password reset link
        $status = Password::sendResetLink([
            'email' => $verificationData['email']
        ]);
        
        if ($status == Password::RESET_LINK_SENT) {
            return redirect()->route('login')
                           ->with('status', 'Password reset link has been sent to your email. Please check your inbox.');
        } else {
            return redirect()->route('password.request')
                           ->withErrors(['email' => 'Unable to send password reset link. Please try again.']);
        }
    }
}
