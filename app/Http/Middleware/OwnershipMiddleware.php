<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OwnershipMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $resourceType = null): Response
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please log in to access this page.');
        }

        $user = Auth::user();
        
        // SuperAdmin, Admin and Staff can access any resource
        if (in_array($user->role, ['SuperAdmin', 'Admin', 'Staff'])) {
            return $next($request);
        }

      
        if ($user->role === 'Alumni') {
            switch ($resourceType) {
                case 'profile':
                    // Alumni can only access their own profile
                    $targetUserId = $request->route('user');
                    if ($targetUserId && $targetUserId != $user->id) {
                        return redirect('/user/dashboard')->with('error', 'You can only access your own profile.');
                    }
                    break;
                    
                case 'alumni':
                    // Alumni can only access their own alumni record
                    $alumniId = $request->route('Alumni') ?? $request->route('id');
                    if ($alumniId) {
                        $alumni = \App\Models\Alumni::find($alumniId);
                        if ($alumni && $alumni->Emailaddress !== $user->email) {
                            return redirect('/user/dashboard')->with('error', 'You can only access your own alumni record.');
                        }
                    }
                    break;
                    
                case 'concern':
                    // Alumni can only access their own concerns
                    $concernParam = $request->route('concern');
                    if ($concernParam) {
                        $concernModel = null;
                        
                        // Handle different types of concern parameter
                        if ($concernParam instanceof \App\Models\AlumniConcern) {
                            // Direct model instance from route model binding
                            $concernModel = $concernParam;
                        } elseif (is_numeric($concernParam)) {
                            // Numeric ID - fetch the model
                            $concernModel = \App\Models\AlumniConcern::find($concernParam);
                        } elseif (is_string($concernParam)) {
                            // String ID - fetch the model
                            $concernModel = \App\Models\AlumniConcern::find($concernParam);
                        } else {
                            // If it's something else (like a collection), try to resolve it
                            if (is_object($concernParam) && method_exists($concernParam, 'getKey')) {
                                $concernModel = $concernParam;
                            } elseif (is_object($concernParam) && method_exists($concernParam, 'first')) {
                                $concernModel = $concernParam->first();
                            }
                        }
                        
                        // Check ownership if we have a valid model
                        if ($concernModel && $concernModel->user_id !== $user->id) {
                            return redirect('/user/alumni-concerns')->with('error', 'You can only access your own concerns.');
                        }
                    }
                    break;
                    
                case 'feedback':
                    // Alumni can only access their own feedback
                    $feedbackParam = $request->route('feedback');
                    
                    if ($feedbackParam) {
                        $feedbackModel = null;
                        
                        // Handle different types of feedback parameter
                        if ($feedbackParam instanceof \App\Models\Feedback) {
                            // Direct model instance from route model binding
                            $feedbackModel = $feedbackParam;
                        } elseif (is_numeric($feedbackParam)) {
                            // Numeric ID - fetch the model
                            $feedbackModel = \App\Models\Feedback::find($feedbackParam);
                        } elseif (is_string($feedbackParam)) {
                            // String ID - fetch the model
                            $feedbackModel = \App\Models\Feedback::find($feedbackParam);
                        } else {
                            // If it's something else (like a collection), try to resolve it
                            // This shouldn't happen with proper route model binding, but let's handle it
                            if (is_object($feedbackParam) && method_exists($feedbackParam, 'getKey')) {
                                $feedbackModel = $feedbackParam;
                            } elseif (is_object($feedbackParam) && method_exists($feedbackParam, 'first')) {
                                $feedbackModel = $feedbackParam->first();
                            }
                        }
                        
                        // Check ownership if we have a valid model
                        if ($feedbackModel && $feedbackModel->user_id !== $user->id) {
                            return redirect('/user/feedback/list')->with('error', 'You can only access your own feedback.');
                        }
                    }
                    break;
            }
        }

        return $next($request);
    }
}