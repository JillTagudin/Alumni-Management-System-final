<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Alumni;
use App\Services\AIAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Mail;

class UserProfileController extends Controller
{
    protected $aiAnalyticsService;
    
    public function __construct(AIAnalyticsService $aiAnalyticsService)
    {
        $this->aiAnalyticsService = $aiAnalyticsService;
    }
    public function edit()
    {
        $user = Auth::user();
        
        return view('user.profile.edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'alumni_id' => ['nullable', 'string', 'max:255'],
            'student_number' => ['required', 'string', 'max:20', Rule::unique('users', 'student_number')->ignore($user->id)],
            'fullname' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'min:1', 'max:150'],
            'gender' => ['nullable', 'string'],
            'course' => ['nullable', 'string', 'max:255'],
            'section' => ['nullable', 'string', 'max:255'],
            'batch' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'digits:11'],
            'address' => ['nullable', 'string'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'occupation' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'facebook_profile' => ['nullable', 'url', 'max:255'],
            'linkedin_profile' => ['nullable', 'url', 'max:255'],
            'twitter_profile' => ['nullable', 'url', 'max:255'],
            'instagram_profile' => ['nullable', 'url', 'max:255'],
        ]);
    
        // Set default values for empty fields (except alumni_id which should be NULL)
        $fieldsToDefault = ['fullname', 'gender', 'course', 'section', 'batch', 'address', 'occupation', 'company'];
        foreach ($fieldsToDefault as $field) {
            if (empty($validated[$field])) {
                $validated[$field] = '(Not Specified)';
            }
        }
        
        // Handle age separately since it's an integer field
        if (empty($validated['age'])) {
            $validated['age'] = null;
        }
        
        // Handle alumni_id separately - convert empty to NULL
        if (empty($validated['alumni_id'])) {
            $validated['alumni_id'] = null;
        }

        try {
            // Handle profile picture upload
            $profilePicturePath = $user->profile_picture;
            
            if ($request->hasFile('profile_picture')) {
                if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            }
            
            // Update user profile
            $user->update([
                'alumni_id' => $validated['alumni_id'],
                'student_number' => $validated['student_number'],
                'fullname' => $validated['fullname'],
                'age' => $validated['age'],
                'gender' => $validated['gender'],
                'course' => $validated['course'],
                'section' => $validated['section'],
                'batch' => $validated['batch'],
                'contact' => $validated['contact'],
                'address' => $validated['address'],
                'email' => $validated['email'],
                'occupation' => $validated['occupation'],
                'company' => $validated['company'],
                'profile_picture' => $profilePicturePath,
                'facebook_profile' => $validated['facebook_profile'],
                'linkedin_profile' => $validated['linkedin_profile'],
                'twitter_profile' => $validated['twitter_profile'],
                'instagram_profile' => $validated['instagram_profile'],
            ]);
            
            // Create or update alumni record for users with Alumni role
            $alumniDataChanged = false;
            if ($user->role === 'Alumni') {
                // Check if alumni record exists by student_number OR user_id
                $existingAlumni = Alumni::where('student_number', $validated['student_number'])
                    ->orWhere('user_id', $user->id)
                    ->first();
                
                if ($existingAlumni) {
                    // FIRST: Ensure the existing alumni record is linked to current user
                    if ($existingAlumni->user_id !== $user->id) {
                        \Log::info("PROFILE UPDATE DEBUG - Linking existing alumni to user", [
                            'alumni_id' => $existingAlumni->id,
                            'old_user_id' => $existingAlumni->user_id,
                            'new_user_id' => $user->id,
                            'student_number' => $existingAlumni->student_number
                        ]);
                        $existingAlumni->update(['user_id' => $user->id]);
                    }
                    
                    // Check if fullname conflicts with records NOT belonging to current user
                    $fullnameConflictRecords = Alumni::where('Fullname', $validated['fullname'])
                        ->where(function($query) use ($user) {
                            $query->where('user_id', '!=', $user->id)
                                  ->orWhereNull('user_id');
                        })
                        ->get();
                    
                    \Log::info("PROFILE UPDATE DEBUG - Fullname conflict check", [
                        'searching_fullname' => $validated['fullname'],
                        'current_user_id' => $user->id,
                        'conflicting_records' => $fullnameConflictRecords->map(function($record) {
                            return [
                                'id' => $record->id,
                                'fullname' => $record->Fullname,
                                'user_id' => $record->user_id,
                                'student_number' => $record->student_number
                            ];
                        })
                    ]);
                    
                    if ($fullnameConflictRecords->count() > 0) {
                        return redirect()->back()->withErrors([
                            'fullname' => 'This full name is already taken by another alumni member. Please use a different name or add a middle initial/suffix to make it unique.'
                        ])->withInput();
                    }
                    
                    // Check if email conflicts with records NOT belonging to current user
                    $emailConflictRecords = Alumni::where('Emailaddress', $validated['email'])
                        ->where(function($query) use ($user) {
                            $query->where('user_id', '!=', $user->id)
                                  ->orWhereNull('user_id');
                        })
                        ->get();
                    
                    \Log::info("PROFILE UPDATE DEBUG - Email conflict check", [
                        'searching_email' => $validated['email'],
                        'current_user_id' => $user->id,
                        'conflicting_records' => $emailConflictRecords->map(function($record) {
                            return [
                                'id' => $record->id,
                                'email' => $record->Emailaddress,
                                'user_id' => $record->user_id,
                                'student_number' => $record->student_number
                            ];
                        })
                    ]);
                    
                    if ($emailConflictRecords->count() > 0) {
                        return redirect()->back()->withErrors([
                            'email' => 'This email address is already taken by another alumni member. Please use a different email address.'
                        ])->withInput();
                    }
                    
                    // Update existing alumni record and ensure it's linked to current user
                    $existingAlumni->update([
                        'user_id' => $user->id, // Ensure it's linked to current user
                        'AlumniID' => $validated['alumni_id'],
                        'student_number' => $validated['student_number'],
                        'Fullname' => $validated['fullname'],
                        'Age' => $validated['age'],
                        'Gender' => $validated['gender'],
                        'Course' => $validated['course'],
                        'Section' => $validated['section'],
                        'Batch' => $validated['batch'],
                        'Contact' => $validated['contact'],
                        'Address' => $validated['address'],
                        'Emailaddress' => $validated['email'],
                        'Occupation' => $validated['occupation'],
                        'Company' => $validated['company']
                    ]);
                    $alumniDataChanged = true;
                } else {
                    // DEBUG: Show ALL existing alumni records with same name and email
                    $allFullnameRecords = Alumni::where('Fullname', $validated['fullname'])->get();
                    $allEmailRecords = Alumni::where('Emailaddress', $validated['email'])->get();
                    
                    \Log::info("PROFILE UPDATE DEBUG - ALL existing records with same fullname", [
                        'searching_fullname' => $validated['fullname'],
                        'current_user_id' => $user->id,
                        'all_records' => $allFullnameRecords->map(function($record) {
                            return [
                                'id' => $record->id,
                                'fullname' => $record->Fullname,
                                'user_id' => $record->user_id,
                                'student_number' => $record->student_number,
                                'email' => $record->Emailaddress
                            ];
                        })
                    ]);
                    
                    \Log::info("PROFILE UPDATE DEBUG - ALL existing records with same email", [
                        'searching_email' => $validated['email'],
                        'current_user_id' => $user->id,
                        'all_records' => $allEmailRecords->map(function($record) {
                            return [
                                'id' => $record->id,
                                'fullname' => $record->Fullname,
                                'user_id' => $record->user_id,
                                'student_number' => $record->student_number,
                                'email' => $record->Emailaddress
                            ];
                        })
                    ]);
                    
                    // Check if fullname conflicts with records NOT belonging to current user
                    $fullnameConflictRecords = Alumni::where('Fullname', $validated['fullname'])
                        ->where(function($query) use ($user) {
                            $query->where('user_id', '!=', $user->id)
                                  ->orWhereNull('user_id');
                        })
                        ->get();
                    
                    \Log::info("PROFILE UPDATE DEBUG - New alumni fullname conflict check", [
                        'searching_fullname' => $validated['fullname'],
                        'current_user_id' => $user->id,
                        'conflicting_records' => $fullnameConflictRecords->map(function($record) {
                            return [
                                'id' => $record->id,
                                'fullname' => $record->Fullname,
                                'user_id' => $record->user_id,
                                'student_number' => $record->student_number
                            ];
                        })
                    ]);
                    
                    if ($fullnameConflictRecords->count() > 0) {
                        return redirect()->back()->withErrors([
                            'fullname' => 'This full name is already taken by another alumni member. Please use a different name or add a middle initial/suffix to make it unique.'
                        ])->withInput();
                    }
                    
                    // Check if email conflicts with records NOT belonging to current user
                    $emailConflictRecords = Alumni::where('Emailaddress', $validated['email'])
                        ->where(function($query) use ($user) {
                            $query->where('user_id', '!=', $user->id)
                                  ->orWhereNull('user_id');
                        })
                        ->get();
                    
                    \Log::info("PROFILE UPDATE DEBUG - New alumni email conflict check", [
                        'searching_email' => $validated['email'],
                        'current_user_id' => $user->id,
                        'conflicting_records' => $emailConflictRecords->map(function($record) {
                            return [
                                'id' => $record->id,
                                'email' => $record->Emailaddress,
                                'user_id' => $record->user_id,
                                'student_number' => $record->student_number
                            ];
                        })
                    ]);
                    
                    if ($emailConflictRecords->count() > 0) {
                        return redirect()->back()->withErrors([
                            'email' => 'This email address is already taken by another alumni member. Please use a different email address.'
                        ])->withInput();
                    }
                    
                    // Create new alumni record only if none exists with this student_number
                    Alumni::create([
                        'user_id' => $user->id,
                        'AlumniID' => $validated['alumni_id'],
                        'student_number' => $validated['student_number'],
                        'Fullname' => $validated['fullname'],
                        'Age' => $validated['age'],
                        'Gender' => $validated['gender'],
                        'Course' => $validated['course'],
                        'Section' => $validated['section'],
                        'Batch' => $validated['batch'],
                        'Contact' => $validated['contact'],
                        'Address' => $validated['address'],
                        'Emailaddress' => $validated['email'],
                        'Occupation' => $validated['occupation'],
                        'Company' => $validated['company'],
                        'membership_status' => 'Pending',
                        'membership_type' => 'Annual'
                    ]);
                    $alumniDataChanged = true;
                }
            }
            
            // Clear AI analytics cache when alumni data changes
            if ($alumniDataChanged) {
                $this->aiAnalyticsService->clearAnalyticsCache();
            }
            
            ActivityLog::log(
                'profile_updated',
                'Profile updated successfully',
                auth()->id(),
                ['alumni_id' => $validated['alumni_id'], 'email' => $validated['email']]
            );
            
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle specific database constraint violations
            $errorMessage = 'Failed to update profile. Please try again.';
            
            if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'alumnis_emailaddress_unique')) {
                $errorMessage = 'This email address is already taken by another alumni member. Please use a different email address.';
            } elseif (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'alumnis_fullname_unique')) {
                $errorMessage = 'This full name is already taken by another alumni member. Please use a different name or add a middle initial/suffix to make it unique.';
            } elseif (str_contains($e->getMessage(), 'Data too long for column \'description\'')) {
                // Truncate the error message for activity log
                $truncatedMessage = substr('Profile update failed: ' . $e->getMessage(), 0, 250);
                ActivityLog::log(
                    'profile_update_error',
                    $truncatedMessage,
                    auth()->id(),
                    ['error' => 'Database constraint violation']
                );
            } else {
                // Log other database errors with truncation
                $truncatedMessage = substr('Profile update failed: ' . $e->getMessage(), 0, 250);
                ActivityLog::log(
                    'profile_update_error',
                    $truncatedMessage,
                    auth()->id(),
                    ['error' => $e->getMessage()]
                );
            }
            
            return redirect()->route('user.profile.edit')
                ->withErrors(['error' => $errorMessage])
                ->withInput();
        } catch (\Exception $e) {
            // Truncate error message for activity log to prevent string length issues
            $truncatedMessage = substr('Profile update failed: ' . $e->getMessage(), 0, 250);
            ActivityLog::log(
                'profile_update_error',
                $truncatedMessage,
                auth()->id(),
                ['error' => $e->getMessage()]
            );
            
            return redirect()->route('user.profile.edit')
                ->withErrors(['error' => 'Failed to update profile. Please try again.'])
                ->withInput();
        }
        
        return redirect()->route('user.profile.edit')
            ->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);
        
        try {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);
            
            ActivityLog::log(
                'password_updated',
                'Password updated successfully',
                auth()->id(),
                ['user_id' => $user->id]
            );
            
            // Send password change notification email
            try {
                Mail::send('emails.password-changed-notification', [
                    'userName' => $user->name,
                    'changeTime' => now()->format('F j, Y \\a\\t g:i A'),
                    'ipAddress' => $request->ip(),
                    'userAgent' => $request->userAgent(),
                    'loginUrl' => route('welcome')
                ], function ($message) use ($user) {
                    $message->to($user->email)
                           ->subject('Security Alert: Password Changed - Alumni Management System');
                });
            } catch (\Exception $e) {
                // Log email error but don't fail the password update
                \Illuminate\Support\Facades\Log::error('Failed to send password change notification email: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }
            
            // Log out the user after password change for security
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('welcome')
                ->with('success', 'Password updated successfully! Please log in with your new password. A security notification has been sent to your email.');
                
        } catch (\Exception $e) {
            ActivityLog::log(
                'password_update_error',
                'Password update failed: ' . $e->getMessage(),
                auth()->id(),
                ['error' => $e->getMessage()]
            );
            
            return redirect()->route('user.profile.edit')
                ->withErrors(['password' => 'Failed to update password. Please try again.'])
                ->withInput();
        }
    }
}
