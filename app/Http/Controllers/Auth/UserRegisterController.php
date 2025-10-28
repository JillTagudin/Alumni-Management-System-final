<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Alumni;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;

class UserRegisterController extends Controller
{
    public function create()
    {
        return view('auth.user-register');
    }

    public function store(Request $request)
    {
        // Enhanced validation with custom rules
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'student_number' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Za-z0-9-]+$/', // Only allow alphanumeric and hyphens
                'unique:users,student_number',
                'unique:alumnis,student_number'
            ]
        ], [
            'student_number.regex' => 'Student number can only contain letters, numbers, and hyphens.',
            'student_number.unique' => 'This student number is already registered.',
            'email.unique' => 'This email address is already registered.',
        ]);

        // Additional server-side validation for student number availability
        $existingUser = User::where('student_number', $request->student_number)->first();
        $existingAlumni = Alumni::where('student_number', $request->student_number)->first();
        
        if ($existingUser || $existingAlumni) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['student_number' => 'This student number is already registered.']);
        }

        // Use database transaction to ensure both User and Alumni records are created together
        DB::beginTransaction();
        
        try {
            // Create user record
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'student_number' => $request->student_number,
                'role' => 'Alumni', // Default role for new users
                'approval_status' => 'pending', // New users require approval
                'email_verified_at' => now(),
            ]);

            // Create alumni record for users with Alumni role
            if ($user->role === 'Alumni') {
                // Generate a unique AlumniID
                $alumniId = 'ALU' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
                
                // Double-check student number doesn't exist (race condition protection)
                $existingAlumni = Alumni::where('student_number', $request->student_number)->first();
                if ($existingAlumni) {
                    throw new \Exception('Student number already exists in alumni records.');
                }
                
                Alumni::create([
                    'user_id' => $user->id,
                    'AlumniID' => $alumniId, // Ensure AlumniID is provided
                    'student_number' => $request->student_number,
                    'Fullname' => $user->name,
                    'Emailaddress' => $user->email,
                    'Age' => null,
                    'Gender' => '(Not Specified)',
                    'Course' => '(Not Specified)',
                    'Section' => '(Not Specified)',
                    'Batch' => '(Not Specified)',
                    'Contact' => '(Not Specified)',
                    'Address' => '(Not Specified)',
                    'Occupation' => '(Not Specified)',
                    'Company' => '(Not Specified)',
                    'membership_status' => 'Pending',
                    'membership_type' => 'Annual'
                ]);
            }

            // Commit the transaction first
            DB::commit();

            // Log user registration after successful commit (outside transaction)
            try {
                ActivityLog::log(
                    'user_registration_pending',
                    'New user registered and awaiting approval',
                    $user->id,
                    ['email' => $user->email, 'name' => $user->name, 'approval_status' => 'pending']
                );
            } catch (\Exception $logError) {
                // If logging fails, just log the error but don't fail the registration
                \Log::error('Failed to log user registration: ' . $logError->getMessage(), [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }

            return redirect()->route('login')->with('status', 'Registration successful! Your account is pending approval. You will be able to log in once approved by an administrator.');
            
        } catch (\Exception $e) {
            // Rollback the transaction on any error
            DB::rollback();
            
            // Provide more specific error messages
            $errorMessage = 'Registration failed. Please try again.';
            if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'student_number')) {
                $errorMessage = 'This student number is already registered. Please use a different student number.';
            } elseif (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'email')) {
                $errorMessage = 'This email address is already registered. Please use a different email address.';
            } elseif (str_contains($e->getMessage(), 'Student number already exists')) {
                $errorMessage = 'This student number is already registered. Please use a different student number.';
            }
            
            // Log the error
            \Log::error('User registration failed: ' . $e->getMessage(), [
                'email' => $request->email,
                'name' => $request->name,
                'student_number' => $request->student_number,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }
}
