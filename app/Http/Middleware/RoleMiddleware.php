<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRoleLevel = $this->getRoleLevel($user->role);

        foreach ($roles as $role) {
            $requiredRoleLevel = $this->getRoleLevel($role);
            if ($userRoleLevel >= $requiredRoleLevel) {
                return $next($request);
            }
        }

        return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
    }

    private function getRoleLevel($role)
    {
        $roleLevels = [
            'Alumni' => 1,
            'Staff' => 2,
            'HR' => 2.5, // HR has slightly higher privileges than Staff
            'Admin' => 3,
            'SuperAdmin' => 4,
        ];

        return $roleLevels[$role] ?? 0;
    }
}