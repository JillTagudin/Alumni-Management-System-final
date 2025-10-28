<?php

namespace App\Http\Controllers;

use App\Models\AlumniConcern;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AlumniConcernController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // User Methods
    public function create()
    {
        return view('user.alumni-concerns.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'category' => ['required', Rule::in(array_keys(AlumniConcern::CATEGORIES))]
        ]);

        $concern = AlumniConcern::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'priority' => 'medium', // Default priority for alumni-submitted concerns
            'status' => 'pending'
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Alumni Concern Submitted',
            'description' => "Submitted concern: {$concern->title}",
            'ip_address' => $request->ip()
        ]);

        // Send notification email to admins (implement later)
        // $this->notifyAdmins($concern);

        return redirect()->route('user.alumni-concerns.index')
            ->with('success', 'Your concern has been submitted successfully. We will respond as soon as possible.');
    }

    public function userIndex()
    {
        $concerns = AlumniConcern::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.alumni-concerns.index', compact('concerns'));
    }

    public function show(AlumniConcern $concern)
    {
        // Check if user owns this concern or is admin/staff
        if ($concern->user_id !== Auth::id() && !in_array(Auth::user()->role, ['Admin', 'Staff', 'SuperAdmin'])) {
            abort(403, 'Unauthorized access to this concern.');
        }

        return view('user.alumni-concerns.show', compact('concern'));
    }

    // Admin Methods
    public function adminIndex(Request $request)
    {
        // Only admin, staff, and superadmin can access
        if (!in_array(Auth::user()->role, ['Admin', 'Staff', 'SuperAdmin'])) {
            abort(403, 'Unauthorized access.');
        }

        $query = AlumniConcern::with(['user', 'responder']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Priority filter hidden - keeping for potential future use
        // if ($request->filled('priority')) {
        //     $query->where('priority', $request->priority);
        // }

        // Search by title or description
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $concerns = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.alumni-concerns.index', compact('concerns'));
    }

    public function adminShow(AlumniConcern $concern)
    {
        // Only admin, staff, and superadmin can access
        if (!in_array(Auth::user()->role, ['Admin', 'Staff', 'SuperAdmin'])) {
            abort(403, 'Unauthorized access.');
        }

        return view('admin.alumni-concerns.show', compact('concern'));
    }

    public function respond(Request $request, AlumniConcern $concern)
    {
        // Only admin, staff, and superadmin can respond
        if (!in_array(Auth::user()->role, ['Admin', 'Staff', 'SuperAdmin'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'admin_response' => 'required|string|max:2000',
            'status' => ['required', Rule::in(array_keys(AlumniConcern::STATUSES))]
        ]);

        $concern->update([
            'admin_response' => $request->admin_response,
            'status' => $request->status,
            'responded_by' => Auth::id(),
            'responded_at' => now()
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Alumni Concern Response',
            'description' => "Responded to concern: {$concern->title}",
            'ip_address' => $request->ip()
        ]);

        // Send notification email to user (implement later)
        // $this->notifyUser($concern);

        return redirect()->route('admin.alumni-concerns.show', $concern)
            ->with('success', 'Response sent successfully.');
    }

    public function updateStatus(Request $request, AlumniConcern $concern)
    {
        // Only admin, staff, and superadmin can update status
        if (!in_array(Auth::user()->role, ['Admin', 'Staff', 'SuperAdmin'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'status' => ['required', Rule::in(array_keys(AlumniConcern::STATUSES))]
        ]);

        $concern->update([
            'status' => $request->status
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Alumni Concern Status Update',
            'description' => "Updated concern status to {$request->status}: {$concern->title}",
            'ip_address' => $request->ip()
        ]);

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    // Statistics for dashboard
    public function getStats()
    {
        return [
            'total' => AlumniConcern::count(),
            'pending' => AlumniConcern::where('status', 'pending')->count(),
            'in_progress' => AlumniConcern::where('status', 'in_progress')->count(),
            'resolved' => AlumniConcern::where('status', 'resolved')->count(),
            'by_category' => AlumniConcern::selectRaw('category, count(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray()
        ];
    }
}