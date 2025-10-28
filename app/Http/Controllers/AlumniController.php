<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Alumni;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\PendingChange;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;  // Add this import for DB::beginTransaction()
use Illuminate\Support\Facades\Hash; // Add this import for Hash::make()
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;

class AlumniController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $this->authorize('viewAny', Alumni::class);
        
        // Join Alumni with User table to get student_number and social media profiles
        $alumnis = Alumni::leftJoin('users', 'alumnis.Emailaddress', '=', 'users.email')
            ->select('alumnis.*', 'users.student_number', 'users.facebook_profile', 'users.linkedin_profile', 'users.twitter_profile', 'users.instagram_profile')
            ->get();
        
        return view('Alumni.index' ,['Alumni' => $alumnis ] );
    }

    public function create()
    {
        $this->authorize('create', Alumni::class);
        
        return view('Alumni.create');
    }   

    public function store(Request $request){
        $this->authorize('create', Alumni::class);
        
        $data = $request->validate([
            'AlumniID' => 'nullable|unique:alumnis,AlumniID',
            'student_number' => 'required|unique:alumnis,student_number',
            'Fullname' => 'required',
            'Age' => 'nullable|integer',
            'Gender'=> 'nullable',
            'Course'=> 'nullable',
            'Section'=> 'required',
            'Batch'=> 'nullable',
            'Contact'=> 'required|digits:11',
            'Address'=> 'nullable',
            'Emailaddress'=> 'required|email|unique:alumnis,Emailaddress|unique:users,email',
            'Occupation'=> 'nullable',
            'Company'=> 'nullable',
        ]);
    
    // Generate AlumniID if not provided
    if (empty($data['AlumniID'])) {
        $data['AlumniID'] = $this->generateUniqueAlumniID();
    }

    // Set default values for empty optional fields
    $fieldsToDefault = ['Gender', 'Course', 'Batch', 'Contact', 'Address', 'Occupation', 'Company'];
    foreach ($fieldsToDefault as $field) {
        if (empty($data[$field])) {
            $data[$field] = '(Not Specified)';
        }
    }
    
    // Handle Age separately since it's an integer field
    if (empty($data['Age'])) {
        $data['Age'] = null;
    }

    // Check if user is staff - if so, create pending change
    if (Auth::user()->role === 'Staff') {
        PendingChange::create([
            'staff_user_id' => Auth::id(),
            'change_type' => 'alumni_creation',
            'change_data' => $data,
            'target_user_email' => $data['Emailaddress'],
            'status' => 'pending'
        ]);
        
        ActivityLog::log(
            'pending_change_submitted',
            "Submitted alumni creation request for {$data['Fullname']} (awaiting approval)",
            Auth::id(),
            [
                'change_type' => 'alumni_creation',
                'target_email' => $data['Emailaddress'],
                'alumni_id' => $data['AlumniID']
            ]
        );
        
        return redirect()->route('staff.pending-changes')->with('success', 'Alumni creation request submitted for approval!');
    }

    // Admin and SuperAdmin users can create directly
    DB::beginTransaction();
    try {
        // Create alumni record
        $newAlumni = Alumni::create($data);
        
        // Generate secure password for new user account
        $generatedPassword = $this->generateSecurePassword();
        
        // Create user account with generated password
        $newUser = User::create([
            'name' => $data['Fullname'],
            'email' => $data['Emailaddress'],
            'password' => Hash::make($generatedPassword),
            'role' => 'Alumni',
            'alumni_id' => $data['AlumniID'],
            'student_number' => $data['student_number'],
            'fullname' => $data['Fullname'],
            'age' => $data['Age'],
            'gender' => $data['Gender'],
            'course' => $data['Course'],
            'section' => $data['Section'],
            'batch' => $data['Batch'],
            'contact' => $data['Contact'],
            'address' => $data['Address'],
            'occupation' => $data['Occupation'],
            'company' => $data['Company'],
            'email_verified_at' => now(), // Auto-verify email for admin-created accounts
        ]);
        
        // Log alumni record creation with password info
        ActivityLog::log(
            'alumni_registered',
            "Created alumni record and user account for {$newAlumni->Fullname}",
            auth()->id(),
            [
                'alumni_id' => $newAlumni->AlumniID,
                'email' => $newAlumni->Emailaddress,
                'user_id' => $newUser->id,
                'generated_password' => $generatedPassword, // Log for admin reference
                'password_strength' => 'High (12 chars, mixed case, numbers, symbols)'
            ]
        );
        
        DB::commit();
        
        // Send welcome email with credentials
        try {
            Mail::send('emails.new-user-account', [
                'userName' => $data['Fullname'],
                'userEmail' => $data['Emailaddress'],
                'generatedPassword' => $generatedPassword,
                'alumniId' => $data['AlumniID'],
                'studentNumber' => $data['student_number'],
                'loginUrl' => route('login'),
                'createdAt' => now()->format('F j, Y \\a\\t g:i A')
            ], function ($message) use ($data) {
                $message->to($data['Emailaddress'])
                       ->subject('Welcome to Alumni Management System - Your Account Credentials');
            });
            
            // Log email sending
            ActivityLog::log(
                'email_sent',
                "Welcome email with credentials sent to {$data['Fullname']}",
                auth()->id(),
                [
                    'email' => $data['Emailaddress'],
                    'email_type' => 'new_user_credentials',
                    'alumni_id' => $data['AlumniID']
                ]
            );
            
            $emailStatus = ' Email with login credentials has been sent to the user.';
        } catch (\Exception $e) {
            // Log email failure
            ActivityLog::log(
                'email_failed',
                "Failed to send welcome email to {$data['Fullname']}: {$e->getMessage()}",
                auth()->id(),
                [
                    'email' => $data['Emailaddress'],
                    'error' => $e->getMessage(),
                    'alumni_id' => $data['AlumniID']
                ]
            );
            
            $emailStatus = ' Warning: Email could not be sent. Please share credentials manually.';
        }
        
        return redirect()->route('Alumni.index')
            ->with('success', 'Alumni record and user account created successfully!' . $emailStatus);
            
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to create alumni record and user account: ' . $e->getMessage());
    }
}

/**
 * Generate a cryptographically secure password
 * Security features:
 * - 12 characters minimum length
 * - Mixed case letters, numbers, and symbols
 * - Cryptographically secure random generation
 * - No ambiguous characters (0, O, l, 1)
 */
private function generateSecurePassword(): string
{
        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; // Removed O
        $lowercase = 'abcdefghijkmnpqrstuvwxyz'; // Removed l
        $numbers = '23456789'; // Removed 0 and 1
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';
        
        $allChars = $uppercase . $lowercase . $numbers . $symbols;
        
        do {
            $password = '';
            
            // Ensure at least one character from each category
            $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
            $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
            $password .= $numbers[random_int(0, strlen($numbers) - 1)];
            $password .= $symbols[random_int(0, strlen($symbols) - 1)];
            
            // Fill remaining 8 characters randomly
            for ($i = 4; $i < 12; $i++) {
                $password .= $allChars[random_int(0, strlen($allChars) - 1)];
            }
            
            // Shuffle the password to randomize character positions
            $password = str_shuffle($password);
            
            // Validate password strength (redundant check but good practice)
        } while (!$this->validatePasswordStrength($password));
        
        return $password;
    }

    /**
     * Validate password meets security requirements
     */
    private function validatePasswordStrength(string $password): bool
    {
        return strlen($password) >= 12 &&
               preg_match('/[A-Z]/', $password) &&
               preg_match('/[a-z]/', $password) &&
               preg_match('/[0-9]/', $password) &&
               preg_match('/[^A-Za-z0-9]/', $password);
    }



    public function edit($id)
    {
        $Alumni = Alumni::findOrFail($id);
        $this->authorize('update', $Alumni);
        return view('Alumni.edit', ['alumni' => $Alumni]);
    }



    
    public function update(Request $request, $id)
    {
        $Alumni = Alumni::findOrFail($id);
        $this->authorize('update', $Alumni);
        
        $data = $request->validate([
            'AlumniID' => 'nullable|string|max:255',
            'student_number' => 'required|unique:alumnis,student_number,' . $Alumni->id,
            'Fullname' => 'nullable',
            'Age' => 'nullable|integer',
            'Gender'=> 'nullable',
            'Course'=> 'nullable',
            'Section'=> 'nullable',
            'Batch'=> 'nullable',
            'Contact'=> 'required|digits:11',
            'Address'=> 'nullable',
            'Emailaddress'=> 'required|email|unique:alumnis,Emailaddress,' . $Alumni->id,
            'Occupation'=> 'nullable',
            'Company'=> 'nullable|string|max:255',
        ]);
    
        // Set default values for empty fields (except AlumniID which should be NULL)
        $fieldsToDefault = ['Fullname', 'Age', 'Gender', 'Course', 'Section', 'Batch', 'Contact', 'Address', 'Occupation', 'Company'];
        foreach ($fieldsToDefault as $field) {
            if (empty($data[$field])) {
                $data[$field] = '(Not Specified)';
            }
        }
        
        // Handle AlumniID separately - convert empty to NULL
        if (empty($data['AlumniID'])) {
            $data['AlumniID'] = null;
        }

        // Check if user is staff - if so, create pending change
        if (Auth::user()->role === 'Staff') {
            PendingChange::create([
                'staff_user_id' => Auth::id(),
                'change_type' => 'alumni_update',
                'change_data' => array_merge($data, ['alumni_id' => $Alumni->id]),
                'target_user_email' => $Alumni->Emailaddress,
                'status' => 'pending'
            ]);
            
            // Log the pending change submission
            ActivityLog::log(
                'pending_change_submitted',
                "Submitted alumni update request for {$Alumni->Fullname} (awaiting approval)",
                Auth::id(),
                [
                    'change_type' => 'alumni_update',
                    'target_email' => $Alumni->Emailaddress,
                    'student_id' => $Alumni->StudentID,
                    'alumni_id' => $Alumni->id
                ]
            );
            
            return redirect()->route('staff.pending-changes')->with('success', 'Alumni update request submitted for approval!');
        }

        // Admin and SuperAdmin users can update directly
        // Find user before updating alumni record (using old email)
        $user = User::where('email', $Alumni->Emailaddress)->first();
    
        // Update Alumni record
        $Alumni->update($data);
    
        // Update the user profile if found
        if ($user) {
            $user->update([
                'alumni_id' => $data['AlumniID'], // Fixed: Changed from StudentID to AlumniID
                'student_number' => $data['student_number'], // Fixed: Use student_number instead of student_id
                'fullname' => $data['Fullname'],
                'age' => $data['Age'],
                'gender' => $data['Gender'],
                'course' => $data['Course'],
                'section' => $data['Section'],
                'batch' => $data['Batch'],
                'contact' => $data['Contact'],
                'address' => $data['Address'],
                'email' => $data['Emailaddress'],
                'occupation' => $data['Occupation'],
                'company' => $data['Company']
            ]);
        }
        
        // Log alumni record update
        ActivityLog::log(
            'alumni_update',
            "Updated alumni record for {$Alumni->Fullname}",
            auth()->id(),
            ['student_id' => $Alumni->StudentID, 'email' => $Alumni->Emailaddress]
        );
    
        return redirect()->route('Alumni.index')->with('success', 'Record updated successfully!');
    }



    public function destroy($id)
    {
        $Alumni = Alumni::findOrFail($id);
        $this->authorize('delete', $Alumni);
        
        // Log alumni record deletion
        ActivityLog::log(
            'alumni_delete',
            "Deleted alumni record for {$Alumni->Fullname}",
            auth()->id(),
            ['student_id' => $Alumni->StudentID, 'email' => $Alumni->Emailaddress]
        );
        
        $Alumni->delete();
        return redirect()->route('Alumni.index')->with('success', 'Record deleted successfully');
    }

    /**
     * Log when a user views an alumni record
     */
    public function logView(Request $request, $id)
    {
        try {
            $alumni = Alumni::findOrFail($id);
            $this->authorize('viewAny', Alumni::class);
            
            // Log alumni record view
            ActivityLog::log(
                'alumni_view',
                "Viewed alumni record for {$alumni->Fullname}",
                auth()->id(),
                [
                    'alumni_id' => $alumni->id,
                    'student_id' => $alumni->StudentID,
                    'email' => $alumni->Emailaddress
                ]
            );
            
            return response()->json(['success' => true, 'message' => 'Alumni view logged successfully']);
        } catch (\Exception $e) {
            \Log::error('Failed to log alumni view', [
                'alumni_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to log alumni view',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log when a user hides an alumni record
     */
    public function logHide(Request $request, $id)
    {
        try {
            $alumni = Alumni::findOrFail($id);
            $this->authorize('viewAny', Alumni::class);
            
            // Log alumni record hide
            ActivityLog::log(
                'alumni_hide',
                "Hid alumni record for {$alumni->Fullname}",
                auth()->id(),
                [
                    'alumni_id' => $alumni->id,
                    'student_id' => $alumni->StudentID,
                    'email' => $alumni->Emailaddress
                ]
            );
            
            return response()->json(['success' => true, 'message' => 'Alumni hide logged successfully']);
        } catch (\Exception $e) {
            \Log::error('Failed to log alumni hide', [
                'alumni_id' => $id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Failed to log alumni hide',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a unique Alumni ID
     */
    private function generateUniqueAlumniID()
    {
        do {
            // Generate a random 6-digit number
            $alumniId = 'ALM' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (Alumni::where('AlumniID', $alumniId)->exists());
        
        return $alumniId;
    }

}
