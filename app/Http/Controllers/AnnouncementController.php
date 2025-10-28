<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\AnnouncementRead;
use App\Models\PendingChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function create()
    {
        return view('announcement');
    }

    public function index()
    {
        // Only show approved announcements to alumni users
        $announcements = Announcement::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('user.announcement', compact('announcements'));
    }
    
    public function show($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        // Mark as read when user views the announcement
        $this->markAsRead($id);
        
        return view('user.announcement-detail', compact('announcement'));
    }
    
    public function showPublic(Announcement $announcement)
    {
        // Public view of announcement for social sharing (no authentication required)
        return view('public.announcement', compact('announcement'));
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
    
    public function getLatestAnnouncements()
    {
        $user = Auth::user();
        
        // Get announcements that the user hasn't read yet
        $unreadAnnouncements = Announcement::whereNotExists(function ($query) use ($user) {
            $query->select('id')
                  ->from('user_announcement_reads')
                  ->where('user_id', $user->id)
                  ->whereColumn('announcement_id', 'announcements.id');
        })
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get(['id', 'title', 'content', 'category', 'created_at']);
        
        return response()->json([
            'success' => true,
            'announcements' => $unreadAnnouncements,
            'count' => $unreadAnnouncements->count(),
            'timestamp' => now()->toISOString()
        ]);
    }

    // Add these methods to the existing AnnouncementController
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'content' => 'required|string',
            'attachments.*' => 'nullable|file|max:51200', // 50MB max per file to match admin form
        ]);
    
        $user = Auth::user();
        
        // Handle attachments
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('announcements', 'public');
                $attachmentPaths[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }
    
        // Determine status based on user role
        $status = 'approved'; // Default for Admin/SuperAdmin
        
        if ($user->role === 'Staff') {
            $status = 'pending'; // Staff announcements need admin approval
        } elseif ($user->role === 'HR') {
            $status = 'pending'; // HR announcements need admin approval
        }
    
        $announcement = Announcement::create([
            'title' => $request->title,
            'category' => $request->category,
            'content' => $request->content,
            'user_id' => $user->id,
            'status' => $status,
            'attachments' => !empty($attachmentPaths) ? json_encode($attachmentPaths) : null,
        ]);
    
        // Check if this is an AJAX request (from admin/superadmin form)
        $isAjaxRequest = $request->ajax() || $request->wantsJson();

        // Handle redirects based on user role and status
        if ($status === 'pending') {
            if ($user->role === 'HR') {
                return redirect()->route('hr.announcement.pending')->with('success', 
                    'Your announcement has been submitted for admin approval.');
            } else {
                // Staff members need approval
                return redirect()->back()->with('success', 
                    'Your announcement has been submitted for admin approval.');
            }
        }

        // For approved announcements (Admin/SuperAdmin)
        if (in_array($user->role, ['Admin', 'SuperAdmin'])) {
            if ($isAjaxRequest) {
                // Return JSON response for AJAX requests (admin modal)
                return response()->json([
                    'success' => true,
                    'message' => 'Announcement created successfully!'
                ]);
            } else {
                // Return regular redirect for non-AJAX requests
                return redirect()->back()->with('success', 'Announcement created successfully!');
            }
        }

        // Default fallback
        return redirect()->back()->with('success', 'Announcement created successfully!');
    }
}
