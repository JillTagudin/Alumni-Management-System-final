<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelcomeController extends Controller
{
    public function index()
    {
        // If user is already authenticated, redirect to appropriate dashboard based on role
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user is not approved
            if ($user->approval_status !== 'approved') {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => $user->approval_status === 'pending' 
                        ? 'Your account is pending approval. Please wait for an administrator to approve your account.'
                        : 'Your account has been denied. Please contact an administrator for more information.'
                ]);
            }
            
            // Redirect based on user role
            if ($user->hasAdminPrivileges()) {
                return redirect()->route('dashboard');
            } elseif ($user->isHR()) {
                return redirect()->route('hr.dashboard');
            } else {
                // Alumni and other users go to user dashboard
                return redirect()->route('user.dashboard');
            }
        }
        
        return view('welcome');
    }
}