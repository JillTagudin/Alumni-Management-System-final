<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    // Update the existing dashboard method
    
    public function dashboard()
    {
        $user = Auth::user();
        
        // Check if user is approved
        if ($user->approval_status !== 'approved') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => $user->approval_status === 'pending' 
                    ? 'Your account is pending approval. Please wait for an administrator to approve your account.'
                    : 'Your account has been denied. Please contact an administrator for more information.'
            ]);
        }
        
        // Get alumni record for the current user
        $alumni = null;
        if ($user->role === 'Alumni') {
            $alumni = Alumni::where('user_id', $user->id)
                           ->orWhere('Emailaddress', $user->email)
                           ->first();
        }
        
        // Load announcements for Alumni and HR users
        $recentAnnouncements = collect();
        if ($user->role === 'Alumni' || $user->role === 'HR') {
            $recentAnnouncements = Announcement::where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }
    
        return view('user.dashboard', compact('user', 'alumni', 'recentAnnouncements'));
    }
}
