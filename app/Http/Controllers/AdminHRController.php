<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminHRController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function hrApprovals()
    {
        // Role protection check
        if (!Auth::user()->hasAdminPrivileges()) {
            abort(403, 'Unauthorized access. Admin privileges required.');
        }

        $hrPendingAnnouncements = Announcement::where('status', 'pending')
            ->whereHas('user', function($query) {
                $query->where('role', 'HR');
            })
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.hr-approvals', compact('hrPendingAnnouncements'));
    }

    public function approveHRAnnouncement(Announcement $announcement)
    {
        // Role protection checks
        if (!Auth::user()->hasAdminPrivileges()) {
            abort(403, 'Unauthorized access. Admin privileges required.');
        }

        if ($announcement->user->role !== 'HR') {
            abort(403, 'This announcement is not from an HR user.');
        }

        if ($announcement->status !== 'pending') {
            abort(403, 'This announcement is not pending approval.');
        }

        $announcement->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'HR announcement approved successfully!');
    }

    public function rejectHRAnnouncement(Request $request, Announcement $announcement)
    {
        // Role protection checks
        if (!Auth::user()->hasAdminPrivileges()) {
            abort(403, 'Unauthorized access. Admin privileges required.');
        }

        if ($announcement->user->role !== 'HR') {
            abort(403, 'This announcement is not from an HR user.');
        }

        if ($announcement->status !== 'pending') {
            abort(403, 'This announcement is not pending approval.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $announcement->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->back()->with('success', 'HR announcement rejected.');
    }
}