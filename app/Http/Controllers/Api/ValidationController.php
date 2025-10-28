<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Alumni;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    /**
     * Check if a student number is available
     */
    public function checkStudentNumber(Request $request)
    {
        $request->validate([
            'student_number' => 'required|string|max:20',
        ]);

        $studentNumber = $request->student_number;

        // Check both users and alumnis tables
        $userExists = User::where('student_number', $studentNumber)->exists();
        $alumniExists = Alumni::where('student_number', $studentNumber)->exists();

        return response()->json([
            'available' => !$userExists && !$alumniExists,
        ]);
    }
}