<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    /**
     * Display a listing of feedback (Admin/Staff view)
     */
    public function index(Request $request)
    {
        $query = Feedback::with(['user', 'respondedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $feedback = $query->paginate(15);

        $totalFeedback = Feedback::count();
        $pendingFeedback = Feedback::where('status', 'pending')->count();
        $resolvedFeedback = Feedback::where('status', 'resolved')->count();
        $highPriorityFeedback = Feedback::where('priority', 'high')->count();

        return view('admin.feedback.index', compact(
            'feedback',
            'totalFeedback',
            'pendingFeedback', 
            'resolvedFeedback',
            'highPriorityFeedback'
        ));
    }

    /**
     * Show the form for creating new feedback (Alumni view)
     */
    public function create()
    {
        return view('user.feedback.create');
    }

    /**
     * Store a newly created feedback
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'category' => ['required', Rule::in(['general', 'technical', 'suggestion', 'complaint', 'feature_request', 'bug_report', 'other'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])]
        ]);

        try {
            DB::beginTransaction();

            $feedback = Feedback::create([
                'user_id' => Auth::id(),
                'subject' => $request->subject,
                'message' => $request->message,
                'category' => $request->category,
                'priority' => $request->priority,
                'status' => 'pending'
            ]);

            // Log the activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'feedback_submitted',
                'description' => 'Submitted feedback: ' . $request->subject,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Feedback submitted successfully. We will review it and get back to you soon.',
                    'feedback_id' => $feedback->id
                ]);
            }

            return redirect()->back()->with('success', 'Feedback submitted successfully. We will review it and get back to you soon.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit feedback. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to submit feedback. Please try again.');
        }
    }

    /**
     * Display the specified feedback
     */
    public function show(Feedback $feedback, Request $request)
    {
        $feedback->load(['user', 'respondedBy']);
        
        // Check if user can view this feedback
        if (!Auth::user()->isAdmin() && !Auth::user()->isStaff() && $feedback->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to feedback.');
        }

        // Log the activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'feedback_viewed',
            'description' => "Viewed feedback #{$feedback->id}: {$feedback->subject}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Return appropriate view based on user type
        if (Auth::user()->isAdmin() || Auth::user()->isStaff()) {
            return view('admin.feedback.show', compact('feedback'));
        } else {
            return view('user.feedback.show', compact('feedback'));
        }
    }

    /**
     * Update feedback status and add admin response
     */
    public function update(Request $request, Feedback $feedback)
    {
        $request->validate([
            'status' => ['required', Rule::in(['pending', 'in_progress', 'resolved', 'closed'])],
            'admin_response' => 'nullable|string|max:5000'
        ]);

        try {
            DB::beginTransaction();

            $feedback->update([
                'status' => $request->status,
                'admin_response' => $request->admin_response,
                'responded_by' => Auth::id(),
                'responded_at' => now()
            ]);

            // Log the activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'feedback_updated',
                'description' => "Updated feedback #{$feedback->id} status to {$request->status}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Feedback updated successfully.',
                    'feedback' => $feedback->fresh(['user', 'respondedBy'])
                ]);
            }

            return redirect()->back()->with('success', 'Feedback updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update feedback. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to update feedback. Please try again.');
        }
    }

    /**
     * Add admin response to feedback
     */
    public function respond(Request $request, Feedback $feedback)
    {
        $request->validate([
            'admin_response' => 'required|string|max:5000'
        ]);

        try {
            DB::beginTransaction();

            $feedback->update([
                'admin_response' => $request->admin_response,
                'responded_by' => Auth::id(),
                'responded_at' => now(),
                'status' => 'in_progress' // Auto-update status when responding
            ]);

            // Log the activity
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'feedback_responded',
                'description' => "Added response to feedback #{$feedback->id}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Response added successfully.',
                    'feedback' => $feedback->fresh(['user', 'respondedBy'])
                ]);
            }

            return redirect()->back()->with('success', 'Response added successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add response. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to add response. Please try again.');
        }
    }



    /**
     * Get user's own feedback (Alumni view)
     */
    public function userFeedback(Request $request)
    {
        $query = Feedback::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $feedback = $query->paginate(10);

        return view('user.feedback.index', compact('feedback'));
    }
}
