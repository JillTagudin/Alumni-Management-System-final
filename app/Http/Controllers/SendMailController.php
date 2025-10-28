<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class SendMailController extends Controller
{
    public function index()
    {
        $totalUsers = User::whereNotNull('email')->count();
        $emailsSentToday = ActivityLog::where('action', 'email_sent')
            ->whereDate('created_at', today())
            ->count();
        
        return view('Sendmail', compact('totalUsers', 'emailsSentToday'));
    }

    public function send(Request $request)
    {
        // Log the start of email sending process
        \Log::info('SendMail process started', [
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->role ?? 'unknown',
            'recipient_type' => $request->recipient_type,
            'email_template' => $request->email_template
        ]);

        $request->validate([
            'recipient_type' => 'required|in:all,specific,role',
            'email' => $request->recipient_type === 'specific' ? 'required|email' : '',
            'role' => $request->recipient_type === 'role' ? 'required|in:Alumni,Staff,HR,Admin,SuperAdmin' : '',
            'email_template' => 'required|in:custom,announcement,newsletter,reminder',
            'subject' => 'required|string|max:255',
            'message' => 'required|string'
        ]);

        try {
            // Get recipients based on type
            if ($request->recipient_type === 'all') {
                $users = User::whereNotNull('email')->get();
            } elseif ($request->recipient_type === 'role') {
                $users = User::whereNotNull('email')->where('role', $request->role)->get();
            } else {
                $users = User::where('email', $request->email)->get();
            }

            // Debug: Check if we found any users
            if ($users->count() === 0) {
                return back()->with('error', 'No users found with the specified criteria. Please check if users exist with valid email addresses.');
            }

            // Determine email template based on selection
            $templateName = $this->getEmailTemplate($request->email_template);
            
            $successCount = 0;
            $failedCount = 0;
            
            foreach ($users as $user) {
                try {
                    // Prepare template data
                    $templateData = [
                        'subject' => $request->subject,
                        'message' => $request->message,
                        'messageContent' => $request->message,
                        'greeting' => "Hello {$user->name},",
                        'userName' => $user->name,
                        'userEmail' => $user->email,
                        'sentAt' => now()->format('F j, Y \\a\\t g:i A')
                    ];

                    // Add template-specific data
                    if ($request->email_template === 'announcement') {
                        $templateData['actionUrl'] = route('dashboard');
                        $templateData['actionText'] = 'View Dashboard';
                    } elseif ($request->email_template === 'newsletter') {
                        $templateData['actionUrl'] = route('announcement');
                        $templateData['actionText'] = 'Read More';
                    } elseif ($request->email_template === 'reminder') {
                        $templateData['actionUrl'] = route('welcome');
                        $templateData['actionText'] = 'Take Action';
                    }

                    Mail::send($templateName, $templateData, function ($message) use ($user, $request) {
                        $message->to($user->email, $user->name)
                               ->subject($request->subject);
                    });
                    
                    $successCount++;
                } catch (\Exception $e) {
                    // Continue with other emails if one fails
                    $failedCount++;
                    \Log::error('Failed to send email to ' . $user->email . ': ' . $e->getMessage());
                    continue;
                }
            }

            // Log the activity
            ActivityLog::log(
                'email_sent',
                "Email sent to {$successCount} recipients using {$request->email_template} template",
                Auth::id(),
                [
                    'recipient_count' => $users->count(),
                    'success_count' => $successCount,
                    'failed_count' => $failedCount,
                    'subject' => $request->subject,
                    'recipient_type' => $request->recipient_type,
                    'template' => $request->email_template
                ]
            );

            // Provide detailed feedback
            if ($successCount > 0 && $failedCount > 0) {
                return back()->with('success', "Email sent to {$successCount} recipients successfully. {$failedCount} emails failed to send. Check logs for details.");
            } elseif ($successCount > 0) {
                return back()->with('success', "Email sent successfully to {$successCount} recipients using {$request->email_template} template!");
            } else {
                return back()->with('error', "Failed to send emails to all {$users->count()} recipients. Check your email configuration and logs for details.");
            }
            
        } catch (\Exception $e) {
            \Log::error('SendMail Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->role ?? 'unknown',
                'request_data' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Get the appropriate email template based on selection
     */
    private function getEmailTemplate($templateType)
    {
        switch ($templateType) {
            case 'announcement':
                return 'emails.announcement';
            case 'newsletter':
                return 'emails.newsletter';
            case 'reminder':
                return 'emails.reminder';
            case 'custom':
            default:
                return 'emails.general-notification';
        }
    }
}
