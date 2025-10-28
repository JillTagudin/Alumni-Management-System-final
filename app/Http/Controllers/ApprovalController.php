<?php

namespace App\Http\Controllers;

use App\Models\PendingChange;
use App\Models\User;
use App\Models\Alumni;
use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\JobOpportunity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ApprovalController extends Controller
{
    /**
     * Display pending changes for admin approval
     */
    public function index(Request $request)
    {
        // Only admins and superadmins can access this
        if (!in_array(Auth::user()->role, ['Admin', 'SuperAdmin'])) {
            abort(403, 'Unauthorized access.');
        }

        $tab = $request->get('tab', 'pending'); // Default to pending tab

        if ($tab === 'user_approvals') {
            $subtab = $request->get('subtab', 'pending'); // Default to pending subtab
            
            // Initialize query for user registrations
            $query = User::with(['approvedBy']);

            // Filter by approval status based on subtab
            if ($subtab === 'pending') {
                $query->where('approval_status', 'pending');
            } elseif ($subtab === 'approved') {
                $query->where('approval_status', 'approved');
            } elseif ($subtab === 'denied') {
                // For denied users, we need to fetch from denied_users table
                $deniedQuery = \DB::table('denied_users');
                
                // Apply global search filter for denied users
                if ($request->filled('global_search')) {
                    $search = $request->global_search;
                    $deniedQuery->where(function($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                          ->orWhere('email', 'LIKE', "%{$search}%")
                          ->orWhere('student_number', 'LIKE', "%{$search}%");
                    });
                }

                // Role filter for denied users
                if ($request->filled('role_filter') && $request->role_filter !== 'all') {
                    $deniedQuery->where('role', $request->role_filter);
                }

                // Date range filter for denied users
                if ($request->filled('date_from')) {
                    $dateFrom = \Carbon\Carbon::parse($request->date_from)
                        ->setTimezone(config('app.timezone', 'UTC'))
                        ->startOfDay();
                    $deniedQuery->where('denied_at', '>=', $dateFrom);
                }

                if ($request->filled('date_to')) {
                    $dateTo = \Carbon\Carbon::parse($request->date_to)
                        ->setTimezone(config('app.timezone', 'UTC'))
                        ->endOfDay();
                    $deniedQuery->where('denied_at', '<=', $dateTo);
                }

                // Get denied users with pagination
                $deniedUsers = $deniedQuery->orderBy('denied_at', 'desc')
                    ->paginate(10);

                // Convert to collection with proper structure for the view
                $users = new \Illuminate\Pagination\LengthAwarePaginator(
                    $deniedUsers->getCollection()->map(function($deniedUser) {
                        return (object) [
                            'id' => $deniedUser->id,
                            'name' => $deniedUser->name,
                            'email' => $deniedUser->email,
                            'role' => $deniedUser->role,
                            'student_number' => $deniedUser->student_number,
                            'approval_status' => 'denied',
                            'created_at' => $deniedUser->denied_at,
                            'approval_notes' => $deniedUser->approval_notes,
                        ];
                    }),
                    $deniedUsers->total(),
                    $deniedUsers->perPage(),
                    $deniedUsers->currentPage(),
                    ['path' => request()->url(), 'pageName' => 'page']
                );
            }

            // Apply filters only for non-denied users (pending/approved)
            if ($subtab !== 'denied') {
                // Global search across multiple fields
                if ($request->filled('global_search')) {
                    $searchTerm = $request->global_search;
                    $query->where(function ($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('student_number', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('fullname', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('contact', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('address', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('course', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('occupation', 'LIKE', "%{$searchTerm}%")
                          ->orWhere('company', 'LIKE', "%{$searchTerm}%");
                    });
                }

                // Role filter
                if ($request->filled('role_filter') && $request->role_filter !== 'all') {
                    $query->where('role', $request->role_filter);
                }

                // Date range filter with timezone conversion
                if ($request->filled('date_from')) {
                    $dateFrom = \Carbon\Carbon::parse($request->date_from)
                        ->setTimezone(config('app.timezone', 'UTC'))
                        ->startOfDay();
                    $query->where('created_at', '>=', $dateFrom);
                }

                if ($request->filled('date_to')) {
                    $dateTo = \Carbon\Carbon::parse($request->date_to)
                        ->setTimezone(config('app.timezone', 'UTC'))
                        ->endOfDay();
                    $query->where('created_at', '<=', $dateTo);
                }
            }

            // Apply ordering and pagination for non-denied users
            if ($subtab !== 'denied') {
                $users = $query->orderBy('created_at', 'desc')->paginate(10);
            }

            // Get counts for each subtab
            $pendingCount = User::where('approval_status', 'pending')->count();
            $approvedCount = User::where('approval_status', 'approved')->count();
            $deniedCount = \DB::table('denied_users')->count();

            // Get filter options for dropdowns
            $roleOptions = User::select('role')
                ->distinct()
                ->whereNotNull('role')
                ->pluck('role')
                ->sort()
                ->values();

            return view('approval.index', [
                'pendingChanges' => collect(), // Empty for user approvals tab
                'approvalHistory' => collect(), // Empty for user approvals tab
                'pendingUsers' => $users, // Renamed to be more generic
                'activeTab' => 'user_approvals',
                'activeSubTab' => $subtab,
                'pendingCount' => $pendingCount,
                'approvedCount' => $approvedCount,
                'deniedCount' => $deniedCount,
                'roleOptions' => $roleOptions,
                'currentFilters' => [
                    'global_search' => $request->global_search,
                    'role_filter' => $request->role_filter,
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                ]
            ]);
        } elseif ($tab === 'history') {
            // Show ALL approval history for both PendingChanges and Announcements
            // Include all records but handle missing staff users gracefully
            $pendingChangesHistory = PendingChange::with(['staffUser', 'reviewedBy'])
                ->whereIn('status', ['approved', 'denied'])
                ->orderBy('reviewed_at', 'desc')
                ->get();
            
            // Get ALL announcement approval/rejection history
            $announcementHistory = Announcement::with(['user'])
                ->whereIn('status', ['approved', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->get();
            
            // Combine both histories
            $combinedHistory = collect();
            
            // Add pending changes to combined history
            foreach ($pendingChangesHistory as $change) {
                $combinedHistory->push($change);
            }
            
            // Add announcements to combined history
            foreach ($announcementHistory as $announcement) {
                $combinedHistory->push((object)[
                    'type' => 'announcement',
                    'id' => $announcement->id,
                    'staff_user' => $announcement->user,
                    'change_type' => 'announcement',
                    'description' => "Announcement: {$announcement->title}",
                    'status' => $announcement->status === 'rejected' ? 'denied' : $announcement->status,
                    'created_at' => $announcement->created_at,
                    'reviewed_at' => $announcement->status === 'approved' ? $announcement->approved_at : $announcement->rejected_at,
                    'review_notes' => $announcement->rejection_reason ?? null,
                    'reviewed_by' => $announcement->status === 'approved' ? 
                        ($announcement->approved_by ? \App\Models\User::find($announcement->approved_by) : null) : 
                        ($announcement->rejected_by ? \App\Models\User::find($announcement->rejected_by) : null)
                ]);
            }
            
            // Sort combined history by reviewed_at date
            $combinedHistory = $combinedHistory->sortByDesc('reviewed_at');
            
            // Paginate the combined results
            $currentPage = request()->get('page', 1);
            $perPage = 10;
            $currentItems = $combinedHistory->slice(($currentPage - 1) * $perPage, $perPage);
            $paginatedHistory = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $combinedHistory->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );
            
            return view('approval.index', [
                'pendingChanges' => collect(), // Empty for history tab
                'approvalHistory' => $paginatedHistory,
                'pendingUsers' => collect(), // Empty for history tab
                'activeTab' => 'history'
            ]);
        } else {
            // Show pending changes (default)
            $pendingChanges = PendingChange::with(['staffUser', 'reviewedBy'])
                ->pending()
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('approval.index', [
                'pendingChanges' => $pendingChanges,
                'approvalHistory' => collect(), // Empty for pending tab
                'pendingUsers' => collect(), // Empty for pending tab
                'activeTab' => 'pending'
            ]);
        }
    }

    /**
     * Approve a pending change
     */
    public function approve(Request $request, PendingChange $pendingChange)
    {
        // Only admins and superadmins can approve
        if (!in_array(Auth::user()->role, ['Admin', 'SuperAdmin'])) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
            }
            abort(403, 'Unauthorized access.');
        }

        if ($pendingChange->status !== 'pending') {
            $message = 'This change has already been reviewed.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        DB::beginTransaction();
        try {
            // Execute the actual change
            $this->executeChange($pendingChange);

            // Update the pending change record
            $pendingChange->update([
                'status' => 'approved',
                'reviewed_by' => Auth::id(),
                'review_notes' => $request->input('review_notes'),
                'reviewed_at' => now()
            ]);

            // Log the approval action
            ActivityLog::log(
                'change_approved',
                "Approved {$pendingChange->change_type} request from {$pendingChange->staffUser->name}",
                Auth::id(),
                [
                    'change_type' => $pendingChange->change_type,
                    'staff_user' => $pendingChange->staffUser->name,
                    'review_notes' => $request->input('review_notes')
                ]
            );

            DB::commit();
            
            $message = 'Change approved successfully.';
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'change_id' => $pendingChange->id,
                    'action' => 'approve'
                ]);
            }
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            $message = 'Failed to approve change: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }
            return redirect()->back()->with('error', $message);
        }
    }

    /**
     * Deny a pending change
     */
    public function deny(Request $request, PendingChange $pendingChange)
    {
        // Only admins and superadmins can deny
        if (!in_array(Auth::user()->role, ['Admin', 'SuperAdmin'])) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
            }
            abort(403, 'Unauthorized access.');
        }

        if ($pendingChange->status !== 'pending') {
            $message = 'This change has already been reviewed.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            return redirect()->back()->with('error', $message);
        }

        $pendingChange->update([
            'status' => 'denied',
            'reviewed_by' => Auth::id(),
            'review_notes' => $request->input('review_notes'),
            'reviewed_at' => now()
        ]);

        // Log the denial action
        ActivityLog::log(
            'change_denied',
            "Denied {$pendingChange->change_type} request from {$pendingChange->staffUser->name}",
            Auth::id(),
            [
                'change_type' => $pendingChange->change_type,
                'staff_user' => $pendingChange->staffUser->name,
                'review_notes' => $request->input('review_notes')
            ]
        );

        $message = 'Change denied successfully.';
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'change_id' => $pendingChange->id,
                'action' => 'deny'
            ]);
        }
        return redirect()->back()->with('success', $message);
    }

    /**
     * Execute the approved change
     */
    private function executeChange(PendingChange $pendingChange)
    {
        switch ($pendingChange->change_type) {
            case 'role_assignment':
                $user = User::where('email', $pendingChange->target_user_email)->first();
                if ($user) {
                    $oldRole = $user->role;
                    $newRole = $pendingChange->change_data['new_role'];
                    $user->update(['role' => $newRole]);
                    
                    // Log the role change
                    ActivityLog::log(
                        'role_update',
                        "Role updated for {$user->name} from {$oldRole} to {$newRole} (via approval)",
                        Auth::id(),
                        [
                            'user_id' => $user->id,
                            'user_name' => $user->name,
                            'old_role' => $oldRole,
                            'new_role' => $newRole
                        ]
                    );
                }
                break;

            case 'job_opportunity_creation':
                // Create the job opportunity from pending change data
                $changeData = $pendingChange->change_data;
                $staffUser = $pendingChange->staffUser;
                
                $jobOpportunity = JobOpportunity::create([
                    'title' => $changeData['title'],
                    'company' => $changeData['company'],
                    'location' => $changeData['location'],
                    'job_type' => $changeData['job_type'],
                    'salary_range' => $changeData['salary_range'],
                    'description' => $changeData['description'],
                    'requirements' => $changeData['requirements'],
                    'application_url' => $changeData['application_url'],
                    'contact_email' => $changeData['contact_email'],
                    'contact_number' => $changeData['contact_number'] ?? null,
                    'application_deadline' => $changeData['application_deadline'],
                    'user_id' => $staffUser->id,
                    'status' => 'approved',
                    'attachments' => $changeData['attachments'] ?? null,
                ]);

                // Log the job opportunity creation
                ActivityLog::log(
                    'job_opportunity_approved',
                    "Approved and created job opportunity: {$jobOpportunity->title} at {$jobOpportunity->company} (requested by {$staffUser->name})",
                    Auth::id(),
                    [
                        'job_opportunity_id' => $jobOpportunity->id,
                        'title' => $jobOpportunity->title,
                        'company' => $jobOpportunity->company,
                        'requested_by' => $staffUser->name,
                        'staff_user_id' => $staffUser->id
                    ]
                );
                break;

            case 'alumni_creation':
                // Set default values for empty optional fields
                $changeData = $pendingChange->change_data;
                $fieldsToDefault = ['Age', 'Gender', 'Course', 'Batch', 'Contact', 'Address', 'Occupation', 'Company'];
                foreach ($fieldsToDefault as $field) {
                    if (empty($changeData[$field])) {
                        $changeData[$field] = '(Not Specified)';
                    }
                }
                
                // Check for existing alumni records
                $existingByStudentNumber = Alumni::where('student_number', $changeData['student_number'])->first();
                $existingByEmail = Alumni::where('Emailaddress', $changeData['Emailaddress'])->first();
                
                if ($existingByStudentNumber || $existingByEmail) {
                    // Update existing record if found
                    $alumni = $existingByStudentNumber ?: $existingByEmail;
                    $alumni->update($changeData);
                    $newAlumni = $alumni;
                } else {
                    // Create new alumni record
                    $newAlumni = Alumni::create($changeData);
                }
                
                // Check if user account already exists
                $existingUser = User::where('email', $changeData['Emailaddress'])->first();
                
                if (!$existingUser) {
                    // Generate secure password for new user account
                    $generatedPassword = $this->generateSecurePassword();
                    
                    // Create user account with generated password
                    $newUser = User::create([
                        'name' => $changeData['Fullname'],
                        'email' => $changeData['Emailaddress'],
                        'password' => Hash::make($generatedPassword),
                        'role' => 'Alumni',
                        'alumni_id' => $changeData['AlumniID'],
                        'student_number' => $changeData['student_number'],
                        'fullname' => $changeData['Fullname'],
                        'age' => $changeData['Age'],
                        'gender' => $changeData['Gender'],
                        'course' => $changeData['Course'],
                        'section' => $changeData['Section'],
                        'batch' => $changeData['Batch'],
                        'contact' => $changeData['Contact'],
                        'address' => $changeData['Address'],
                        'occupation' => $changeData['Occupation'],
                        'company' => $changeData['Company'],
                        'email_verified_at' => now(), // Auto-verify email for approved accounts
                    ]);
                    
                    // Send welcome email with credentials
                    try {
                        Mail::send('emails.new-user-account', [
                            'userName' => $changeData['Fullname'],
                            'userEmail' => $changeData['Emailaddress'],
                            'generatedPassword' => $generatedPassword,
                            'alumniId' => $changeData['AlumniID'],
                            'studentNumber' => $changeData['student_number'],
                            'loginUrl' => route('login'),
                            'createdAt' => now()->format('F j, Y \\a\\t g:i A')
                        ], function ($message) use ($changeData) {
                            $message->to($changeData['Emailaddress'])
                                   ->subject('Welcome to Alumni Management System - Your Account Credentials');
                        });
                        
                        // Log email sending
                        ActivityLog::log(
                            'email_sent',
                            "Welcome email with credentials sent to {$changeData['Fullname']} (via approval)",
                            Auth::id(),
                            [
                                'email' => $changeData['Emailaddress'],
                                'email_type' => 'new_user_credentials',
                                'alumni_id' => $changeData['AlumniID'],
                                'approved_by' => Auth::user()->name
                            ]
                        );
                    } catch (\Exception $e) {
                        // Log email failure
                        ActivityLog::log(
                            'email_failed',
                            "Failed to send welcome email to {$changeData['Fullname']} (via approval): {$e->getMessage()}",
                            Auth::id(),
                            [
                                'email' => $changeData['Emailaddress'],
                                'error' => $e->getMessage(),
                                'alumni_id' => $changeData['AlumniID'],
                                'approved_by' => Auth::user()->name
                            ]
                        );
                    }
                    
                    // Log alumni record creation with user account and password info
                    ActivityLog::log(
                        'alumni_registered',
                        "Created alumni record and user account for {$newAlumni->Fullname} (via approval)",
                        Auth::id(),
                        [
                            'alumni_id' => $newAlumni->AlumniID,
                            'email' => $newAlumni->Emailaddress,
                            'user_id' => $newUser->id,
                            'generated_password' => $generatedPassword, // Log for admin reference
                            'password_strength' => 'High (12 chars, mixed case, numbers, symbols)',
                            'approved_by' => Auth::user()->name,
                            'staff_requester' => $pendingChange->staffUser->name
                        ]
                    );
                } else {
                    // Update existing user account with alumni data
                    $existingUser->update([
                        'alumni_id' => $changeData['AlumniID'],
                        'student_number' => $changeData['student_number'],
                        'fullname' => $changeData['Fullname'],
                        'age' => $changeData['Age'],
                        'gender' => $changeData['Gender'],
                        'course' => $changeData['Course'],
                        'section' => $changeData['Section'],
                        'batch' => $changeData['Batch'],
                        'contact' => $changeData['Contact'],
                        'address' => $changeData['Address'],
                        'occupation' => $changeData['Occupation'],
                        'company' => $changeData['Company'],
                    ]);
                    
                    // Log alumni record creation with existing user update
                    ActivityLog::log(
                        'alumni_registered',
                        "Created alumni record and updated existing user account for {$newAlumni->Fullname} (via approval)",
                        Auth::id(),
                        [
                            'alumni_id' => $newAlumni->AlumniID,
                            'email' => $newAlumni->Emailaddress,
                            'user_id' => $existingUser->id,
                            'user_account_status' => 'Updated existing account',
                            'approved_by' => Auth::user()->name,
                            'staff_requester' => $pendingChange->staffUser->name
                        ]
                    );
                }
                break;

            case 'alumni_update':
                $alumni = Alumni::find($pendingChange->change_data['alumni_id']);
                if ($alumni) {
                    $updateData = $pendingChange->change_data;
                    unset($updateData['alumni_id']); // Remove alumni_id from update data
                    $alumni->update($updateData);
                    
                    // Also update the associated user if exists
                    $user = User::where('email', $alumni->Emailaddress)->first();
                    if ($user) {
                        $user->update([
                            'alumni_id' => $updateData['AlumniID'],  // Fixed: Changed from StudentID to AlumniID
                            'student_number' => $updateData['student_number'] ?? null,  // Added student_number field
                            'fullname' => $updateData['Fullname'],
                            'age' => $updateData['Age'],
                            'gender' => $updateData['Gender'],
                            'course' => $updateData['Course'],
                            'section' => $updateData['Section'],
                            'batch' => $updateData['Batch'],
                            'contact' => $updateData['Contact'],
                            'address' => $updateData['Address'],
                            'email' => $updateData['Emailaddress'],
                            'occupation' => $updateData['Occupation'],
                            'company' => $updateData['Company'] ?? null
                        ]);
                    }
                    
                    // Log alumni record update
                    ActivityLog::log(
                        'alumni_update',
                        "Updated alumni record for {$alumni->Fullname} (via approval)",
                        Auth::id(),
                        ['alumni_id' => $alumni->AlumniID, 'email' => $alumni->Emailaddress]
                    );
                }
                break;

            case 'announcement_creation':
                $announcement = Announcement::create($pendingChange->change_data);
                
                // Log announcement creation
                ActivityLog::log(
                    'announcement_created',
                    "Created announcement: {$announcement->title} (via approval)",
                    Auth::id(),
                    ['title' => $announcement->title, 'category' => $announcement->category]
                );
                break;

            case 'user_creation':
                $newUser = User::create([
                    'name' => $pendingChange->change_data['name'],
                    'email' => $pendingChange->change_data['email'],
                    'password' => Hash::make($pendingChange->change_data['password']),
                    'role' => $pendingChange->change_data['role']
                ]);
                
                // Log user creation
                ActivityLog::log(
                    'user_registered',
                    "Created user account for {$newUser->name} (via approval)",
                    Auth::id(),
                    ['user_id' => $newUser->id, 'email' => $newUser->email, 'role' => $newUser->role]
                );
                break;

            case 'user_update':
                $user = User::where('email', $pendingChange->target_user_email)->first();
                if ($user) {
                    $updateData = $pendingChange->change_data;
                    if (isset($updateData['password'])) {
                        $updateData['password'] = Hash::make($updateData['password']);
                    }
                    $user->update($updateData);
                    
                    // Log user update
                    ActivityLog::log(
                        'user_updated',
                        "Updated user account for {$user->name} (via approval)",
                        Auth::id(),
                        ['user_id' => $user->id, 'email' => $user->email, 'updated_fields' => array_keys($pendingChange->change_data)]
                    );
                }
                break;

            default:
                throw new \Exception('Unknown change type: ' . $pendingChange->change_type);
        }
    }

    /**
     * Generate a cryptographically secure password (same as AlumniController)
     */
    private function generateSecurePassword(): string
    {
        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lowercase = 'abcdefghijkmnpqrstuvwxyz';
        $numbers = '23456789';
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

    /**
     * Show staff user's pending changes
     */
    public function staffPendingChanges(Request $request)
    {
        // Only staff can access this
        if (Auth::user()->role !== 'Staff') {
            abort(403, 'Unauthorized access.');
        }

        $query = PendingChange::with(['reviewedBy'])
            ->where('staff_user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        // Filter by date range with timezone conversion
        if ($request->filled('date_from')) {
            // Convert from Asia/Manila to UTC for database query
            $dateFrom = \Carbon\Carbon::createFromFormat('Y-m-d', $request->date_from, 'Asia/Manila')
                ->startOfDay()
                ->utc();
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($request->filled('date_to')) {
            // Convert from Asia/Manila to UTC for database query
            $dateTo = \Carbon\Carbon::createFromFormat('Y-m-d', $request->date_to, 'Asia/Manila')
                ->endOfDay()
                ->utc();
            $query->where('created_at', '<=', $dateTo);
        }

        $pendingChanges = $query->paginate(10);

        return view('approval.staff-pending', compact('pendingChanges'));
    }

    /**
     * Get staff pending changes for AJAX requests
     */
    public function getStaffPendingChanges(Request $request)
    {
        $query = PendingChange::where('staff_user_id', Auth::id())
            ->pending()
            ->with(['staffUser', 'adminUser'])
            ->orderBy('created_at', 'desc');

        // Filter by date range with timezone conversion
        if ($request->filled('date_from')) {
            // Convert from Asia/Manila to UTC for database query
            $dateFrom = \Carbon\Carbon::createFromFormat('Y-m-d', $request->date_from, 'Asia/Manila')
                ->startOfDay()
                ->utc();
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($request->filled('date_to')) {
            // Convert from Asia/Manila to UTC for database query
            $dateTo = \Carbon\Carbon::createFromFormat('Y-m-d', $request->date_to, 'Asia/Manila')
                ->endOfDay()
                ->utc();
            $query->where('created_at', '<=', $dateTo);
        }

        $pendingChanges = $query->paginate(10);

        $html = view('approval.partials.staff-pending-table', compact('pendingChanges'))->render();
        $paginationHtml = $pendingChanges->links()->render();

        return response()->json([
            'html' => $html,
            'pagination' => $paginationHtml,
            'count' => $pendingChanges->total()
        ]);
    }

    /**
     * Get pending counts for notification badges
     */
    public function getPendingCounts()
    {
        $user = Auth::user();
        
        if (in_array($user->role, ['Admin', 'SuperAdmin'])) {
            $globalPendingCount = PendingChange::where('status', 'pending')->count();
            return response()->json([
                'success' => true,
                'global_pending_count' => $globalPendingCount
            ]);
        } elseif ($user->role === 'Staff') {
            $myPendingCount = PendingChange::where('staff_user_id', $user->id)
                ->where('status', 'pending')
                ->count();
            return response()->json([
                'success' => true,
                'my_pending_count' => $myPendingCount
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 403);
    }
    
    public function getAdminPendingCount()
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['Admin', 'SuperAdmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
        
        $count = PendingChange::where('status', 'pending')->count();
        
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Approve a user registration
     */
    public function approveUser(Request $request, $userId)
    {
        // Only admins and superadmins can approve users
        if (!in_array(Auth::user()->role, ['Admin', 'SuperAdmin'])) {
            abort(403, 'Unauthorized access.');
        }

        $user = User::findOrFail($userId);

        if ($user->approval_status !== 'pending') {
            return redirect()->back()->with('error', 'User is not pending approval.');
        }

        $user->approve(Auth::id(), $request->input('approval_notes'));

        // Send approval email to user
        Mail::send('emails.user-approved', [
            'userName' => $user->name,
            'userEmail' => $user->email,
            'approvedAt' => now()->format('F j, Y \a\t g:i A'),
            'approvalNotes' => $request->input('approval_notes'),
            'loginUrl' => route('login'),
        ], function ($message) use ($user) {
            $message->to($user->email)->subject('Account Approved - Alumni Management System');
        });

        // Log the approval action
        ActivityLog::log(
            'user_approved',
            "Approved user registration for {$user->name} ({$user->email})",
            Auth::id(),
            [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'approval_notes' => $request->input('approval_notes')
            ]
        );

        return redirect()->back()->with('success', 'User approved successfully.');
    }

    /**
     * Deny a user registration
     */
    public function denyUser(Request $request, $userId)
    {
        // Only admins and superadmins can deny users
        if (!in_array(Auth::user()->role, ['Admin', 'SuperAdmin'])) {
            abort(403, 'Unauthorized access.');
        }

        $user = User::findOrFail($userId);

        if ($user->approval_status !== 'pending') {
            return redirect()->back()->with('error', 'User is not pending approval.');
        }

        // Use database transaction to ensure all operations complete successfully
        try {
            \DB::beginTransaction();

            // Find associated alumni record if exists
            $alumni = Alumni::where('user_id', $user->id)->first();

            // Store original data for logging before deletion
            $originalEmail = $user->email;
            $originalName = $user->name;
            $originalUserId = $user->id;

            // Create denied user record for audit purposes
            \DB::table('denied_users')->insert([
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'student_number' => $user->student_number,
                'approval_notes' => $request->input('approval_notes'),
                'denied_by' => Auth::id(),
                'denied_at' => now(),
                'original_created_at' => $user->created_at,
                'original_user_data' => json_encode($user->toArray()),
                'original_alumni_data' => $alumni ? json_encode($alumni->toArray()) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Send denial email to user before deletion
            try {
                Mail::send('emails.user-denied', [
                    'userName' => $user->name,
                    'userEmail' => $user->email,
                    'deniedAt' => now()->format('F j, Y \a\t g:i A'),
                    'denialReason' => $request->input('approval_notes'),
                ], function ($message) use ($user) {
                    $message->to($user->email)->subject('Registration Denied - Alumni Management System');
                });
            } catch (\Exception $e) {
                \Log::error('Failed to send denial email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
                // Continue with deletion even if email fails
            }

            // Delete associated alumni record if exists
            if ($alumni) {
                $alumniDeleted = $alumni->delete();
                
                if (!$alumniDeleted) {
                    throw new \Exception('Failed to delete alumni record');
                }
                
                // Log alumni record deletion
                ActivityLog::log(
                    'alumni_deleted_denied',
                    "Deleted alumni record for denied user {$originalName} (email: {$originalEmail})",
                    Auth::id(),
                    [
                        'user_id' => $originalUserId,
                        'alumni_id' => $alumni->id,
                        'email' => $originalEmail
                    ]
                );
            }

            // Delete the user record completely
            $userDeleted = $user->delete();
            
            if (!$userDeleted) {
                throw new \Exception('Failed to delete user record');
            }

            // Log the denial action
            ActivityLog::log(
                'user_denied',
                "Denied and deleted user registration for {$originalName} (email: {$originalEmail})",
                Auth::id(),
                [
                    'user_id' => $originalUserId,
                    'user_name' => $originalName,
                    'email' => $originalEmail,
                    'approval_notes' => $request->input('approval_notes')
                ]
            );

            // Commit the transaction
            \DB::commit();

            return redirect()->back()->with('success', 'User denied successfully.');

        } catch (\Exception $e) {
            // Rollback the transaction on any error
            \DB::rollback();
            
            \Log::error('Failed to deny and delete user', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to deny user. Please try again or contact system administrator.');
        }
    }
}