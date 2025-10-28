<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Alumni;
use App\Services\AIAnalyticsService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    protected $aiAnalyticsService;
    
    public function __construct(AIAnalyticsService $aiAnalyticsService)
    {
        $this->aiAnalyticsService = $aiAnalyticsService;
    }
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'Alumni', // Default role for new users
            'email_verified_at' => now(), // Set as verified since we're using approval flow
            'approval_status' => 'pending', // Set to pending for admin approval
        ]);

        event(new Registered($user));

        // Create alumni record for users with Alumni role
        if ($user->role === 'Alumni') {
            Alumni::create([
                'user_id' => $user->id,
                'student_number' => '', // Will be filled during profile update
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
            
            // Clear AI analytics cache when new alumni record is created
            $this->aiAnalyticsService->clearAnalyticsCache();
        }

        // Log user registration as pending
        ActivityLog::log(
            'user_registered_pending',
            'New user registered and awaiting admin approval',
            $user->id,
            ['email' => $user->email, 'name' => $user->name, 'approval_status' => 'pending']
        );

        // Redirect to login with success message about pending approval
        return redirect(route('login'))->with('status', 'Registration successful! Your account is pending for approval. Please wait for an administrator to approve your account, once it\'s approved you will receive a notification in your email.');
    }
}
