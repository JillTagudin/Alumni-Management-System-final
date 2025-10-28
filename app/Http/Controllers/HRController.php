<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementRead;
use App\Models\PendingChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HRController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:HR']);
    }

    public function dashboard()
    {
        // Role protection check
        if (!Auth::user()->isHR()) {
            abort(403, 'Unauthorized access. HR role required.');
        }

        $user = Auth::user();
        
        $data = [
            'pendingAnnouncements' => Announcement::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'approvedAnnouncements' => Announcement::where('user_id', $user->id)
                ->where('status', 'approved')
                ->count(),
            'announcements' => Announcement::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
        ];

        return view('hr.dashboard', $data);
    }

    // HR-specific announcement viewing methods
    public function announcements()
    {
        // Role protection check
        if (!Auth::user()->isHR()) {
            abort(403, 'Unauthorized access. HR role required.');
        }

        // HR users can view all approved announcements
        $announcements = Announcement::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hr.announcement.index', compact('announcements'));
    }

    public function showAnnouncement($id)
    {
        // Role protection check
        if (!Auth::user()->isHR()) {
            abort(403, 'Unauthorized access. HR role required.');
        }

        $announcement = Announcement::findOrFail($id);
        
        // HR users can view approved announcements OR their own pending announcements
        if ($announcement->status !== 'approved' && 
            !($announcement->status === 'pending' && $announcement->user_id === Auth::id())) {
            abort(403, 'You can only view approved announcements or your own pending announcements.');
        }
        
        // Mark as read when HR user views the announcement
        $this->markAsRead($id);
        
        return view('hr.announcement.show', compact('announcement'));
    }

    public function markAsRead($announcementId)
    {
        $user = Auth::user();
        
        // Check if already marked as read
        $existingRead = AnnouncementRead::where('user_id', $user->id)
                                       ->where('announcement_id', $announcementId)
                                       ->first();
        
        if (!$existingRead) {
            AnnouncementRead::create([
                'user_id' => $user->id,
                'announcement_id' => $announcementId,
                'read_at' => now()
            ]);
        }
        
        return response()->json(['success' => true]);
    }

    public function pendingAnnouncements()
    {
        // Role protection check
        if (!Auth::user()->isHR()) {
            abort(403, 'Unauthorized access. HR role required.');
        }

        $pendingAnnouncements = Announcement::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('hr.announcement.pending', compact('pendingAnnouncements'));
    }

    public function pendingJobPostings()
    {
        // Role protection check
        if (!Auth::user()->isHR()) {
            abort(403, 'Unauthorized access. HR role required.');
        }

        $pendingJobPostings = PendingChange::with(['reviewedBy'])
            ->where('staff_user_id', Auth::id())
            ->where('change_type', 'job_opportunity_creation')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('hr.job-posting.pending', compact('pendingJobPostings'));
    }

    public function createAnnouncement()
    {
        // Role protection check
        if (!Auth::user()->isHR()) {
            abort(403, 'Unauthorized access. HR role required.');
        }

        return view('hr.announcement.create');
    }

    public function editAnnouncement(Announcement $announcement)
    {
        // Role protection checks
        if (!Auth::user()->isHR()) {
            abort(403, 'Unauthorized access. HR role required.');
        }

        if ($announcement->user_id !== Auth::id()) {
            abort(403, 'You can only edit your own announcements.');
        }

        if ($announcement->status !== 'pending') {
            abort(403, 'You can only edit pending announcements.');
        }

        return view('hr.announcement.edit', compact('announcement'));
    }

    // HR-specific job opportunity viewing methods
    public function jobOpportunities()
    {
        // Role protection check
        if (!Auth::user()->isHR()) {
            abort(403, 'Unauthorized access. HR role required.');
        }

        // HR users can view all approved job opportunities
        $jobOpportunities = \App\Models\JobOpportunity::where('status', 'approved')
            ->where(function($q) {
                $q->whereNull('application_deadline')
                  ->orWhere('application_deadline', '>=', now()->toDateString());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('hr.job-opportunity.index', compact('jobOpportunities'));
    }

    public function showJobOpportunity($id)
    {
        // Role protection check
        if (!Auth::user()->isHR()) {
            abort(403, 'Unauthorized access. HR role required.');
        }

        $jobOpportunity = \App\Models\JobOpportunity::findOrFail($id);
        
        // HR users can view approved job opportunities OR their own pending job opportunities
        if ($jobOpportunity->status !== 'approved' && 
            !($jobOpportunity->status === 'pending' && $jobOpportunity->user_id === Auth::id())) {
            abort(403, 'You can only view approved job opportunities or your own pending job opportunities.');
        }
        
        return view('hr.job-opportunity.show', compact('jobOpportunity'));
    }
}