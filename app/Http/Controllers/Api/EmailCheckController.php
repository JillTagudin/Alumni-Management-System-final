<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EmailCheckController extends Controller
{
    /**
     * Check if email is available for registration
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->input('email');
        
        // Check if email already exists in users table
        $exists = User::where('email', $email)->exists();
        
        return response()->json([
            'available' => !$exists,
            'email' => $email,
            'message' => $exists ? 'Email is already registered' : 'Email is available'
        ]);
    }
}