<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request with enhanced security checks.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            $this->logSecurityEvent('Unauthenticated SuperAdmin access attempt', $request);
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user has SuperAdmin role
        if ($user->role !== 'SuperAdmin') {
            $this->logSecurityEvent('Unauthorized SuperAdmin access attempt', $request, $user);
            abort(403, 'Access denied. SuperAdmin privileges required.');
        }

        // Enhanced security checks for URL manipulation
        $this->performSecurityChecks($request, $user);

        // Log successful SuperAdmin access
        $this->logActivityAccess($request, $user);

        return $next($request);
    }

    /**
     * Perform enhanced security checks for URL manipulation detection.
     */
    private function performSecurityChecks(Request $request, $user): void
    {
        // Check for suspicious URL patterns
        $suspiciousPatterns = [
            '/\.\.\//i',  // Directory traversal
            '/\%2e\%2e\%2f/i',  // Encoded directory traversal
            '/script[^>]*>.*?<\/script>/i',  // Script injection
            '/javascript:/i',  // JavaScript protocol
            '/data:/i',  // Data protocol
            '/vbscript:/i',  // VBScript protocol
            '/file:/i',  // File protocol
            '/ftp:/i',   // FTP protocol
        ];

        $fullUrl = $request->fullUrl();
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $fullUrl)) {
                $this->logSecurityEvent('Suspicious URL pattern detected', $request, $user, [
                    'pattern' => $pattern,
                    'url' => $fullUrl
                ]);
                abort(403, 'Suspicious request detected.');
            }
        }

        // Check for parameter tampering
        $this->checkParameterTampering($request, $user);

        // Validate referrer for sensitive operations
        $this->validateReferrer($request, $user);

        // Check for rate limiting
        $this->checkRateLimit($request, $user);
    }

    /**
     * Check for potential parameter tampering.
     */
    private function checkParameterTampering(Request $request, $user): void
    {
        $suspiciousParams = ['admin', 'root', 'system', 'debug', 'test', 'dev', 'config', 'password'];
        $allParams = array_merge($request->query(), $request->input());

        foreach ($allParams as $key => $value) {
            // Check for suspicious parameter names
            if (in_array(strtolower($key), $suspiciousParams)) {
                $this->logSecurityEvent('Suspicious parameter detected', $request, $user, [
                    'parameter' => $key,
                    'value' => is_string($value) ? substr($value, 0, 100) : $value
                ]);
            }

            // Check for SQL injection patterns
            if (is_string($value) && preg_match('/union|select|insert|update|delete|drop|create|alter|exec|script/i', $value)) {
                $this->logSecurityEvent('Potential SQL injection attempt', $request, $user, [
                    'parameter' => $key,
                    'value' => substr($value, 0, 100)
                ]);
                abort(403, 'Malicious request detected.');
            }

            // Check for XSS patterns
            if (is_string($value) && preg_match('/<script|javascript:|on\w+=/i', $value)) {
                $this->logSecurityEvent('Potential XSS attempt', $request, $user, [
                    'parameter' => $key,
                    'value' => substr($value, 0, 100)
                ]);
                abort(403, 'Malicious request detected.');
            }
        }
    }

    /**
     * Validate referrer for sensitive operations.
     */
    private function validateReferrer(Request $request, $user): void
    {
        $referrer = $request->header('referer');
        $host = $request->getHost();

        // For POST, PUT, DELETE requests, ensure referrer is from same domain
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            if (!$referrer || !str_contains($referrer, $host)) {
                $this->logSecurityEvent('Invalid referrer for sensitive operation', $request, $user, [
                    'referrer' => $referrer,
                    'expected_host' => $host,
                    'method' => $request->method()
                ]);
                // Log but don't block - some legitimate requests may not have referrer
            }
        }
    }

    /**
     * Check for rate limiting (basic implementation).
     */
    private function checkRateLimit(Request $request, $user): void
    {
        $key = 'superadmin_access_' . $user->id;
        $maxAttempts = 100; // Max 100 requests per minute
        $decayMinutes = 1;

        if (cache()->has($key)) {
            $attempts = cache()->get($key);
            if ($attempts >= $maxAttempts) {
                $this->logSecurityEvent('Rate limit exceeded', $request, $user, [
                    'attempts' => $attempts,
                    'max_attempts' => $maxAttempts
                ]);
                abort(429, 'Too many requests. Please try again later.');
            }
            cache()->put($key, $attempts + 1, now()->addMinutes($decayMinutes));
        } else {
            cache()->put($key, 1, now()->addMinutes($decayMinutes));
        }
    }

    /**
     * Log successful activity access.
     */
    private function logActivityAccess(Request $request, $user): void
    {
        try {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'SuperAdmin Access',
                'description' => 'SuperAdmin accessed: ' . $request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log SuperAdmin activity', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Log security events with detailed information.
     */
    private function logSecurityEvent(string $event, Request $request, $user = null, array $additional = []): void
    {
        $logData = [
            'event' => $event,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'timestamp' => now()->toDateTimeString(),
        ];

        if ($user) {
            $logData['user_id'] = $user->id;
            $logData['user_email'] = $user->email;
            $logData['user_role'] = $user->role;
        }

        $logData = array_merge($logData, $additional);

        Log::warning('SuperAdmin Security Event', $logData);

        // Also log to activity log if user exists
        if ($user) {
            try {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'Security Alert',
                    'description' => $event,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'created_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Fail silently to prevent blocking the security check
            }
        }
    }
}