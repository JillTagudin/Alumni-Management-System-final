<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseInterface;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): ResponseInterface
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Allow Admin, Staff, HR, and SuperAdmin roles
        if (!in_array($user->role, ['Admin', 'Staff', 'HR', 'SuperAdmin'])) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
