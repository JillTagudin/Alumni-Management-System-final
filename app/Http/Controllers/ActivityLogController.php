<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the activity logs.
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by user if specified
        if ($request->filled('user_filter')) {
            $query->where('user_id', $request->user_filter);
        }

        // Filter by role if specified
        if ($request->filled('role_filter')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role_filter);
            });
        }

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

        // Global search across all fields
        if ($request->filled('global_search')) {
            $searchTerm = $request->global_search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('action', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('ip_address', 'like', "%{$searchTerm}%")
                  ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                      $userQuery->where('name', 'like', "%{$searchTerm}%")
                                ->orWhere('email', 'like', "%{$searchTerm}%")
                                ->orWhere('fullname', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $logs = $query->paginate(50);
        
        // Get only users who have activity logs for better performance
        $users = \App\Models\User::whereHas('activityLogs')
            ->orderBy('name')
            ->get();
            
        // Get available roles for filtering
        $roles = \App\Models\User::select('role')
            ->whereNotNull('role')
            ->distinct()
            ->orderBy('role')
            ->pluck('role');

        // Calculate statistics for the dashboard cards
        $totalLogs = ActivityLog::count();
        $todayLogs = ActivityLog::whereDate('created_at', today())->count();
        
        // Get recent action types with counts
        $recentActions = ActivityLog::select('action')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
        
        // Get active users with their activity counts (users who performed actions in last 30 days)
        $activeUsers = ActivityLog::select('user_id')
            ->selectRaw('COUNT(*) as activity_count')
            ->with('user')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('user_id')
            ->orderBy('activity_count', 'desc')
            ->limit(10)
            ->get();

        return view('ActivityLogs.index', compact('logs', 'users', 'roles', 'totalLogs', 'todayLogs', 'recentActions', 'activeUsers'));
    }

    /**
     * Show the specified activity log.
     */
    public function show(ActivityLog $log, Request $request)
    {
        $log->load('user');
        
        // If it's an AJAX request or requesting JSON, return JSON response
        if ($request->wantsJson() || $request->is('*/details')) {
            return response()->json([
                'user' => $log->user ? $log->user->name : 'System',
                'action' => strtoupper($log->action),
                'created_at' => $log->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i:s A'),
                'ip_address' => $log->ip_address ?: 'Not recorded',
                'description' => $log->description ?: 'No description available',
                'user_agent' => $log->user_agent ?: 'Not recorded'
            ]);
        }
        
        return view('ActivityLogs.show', compact('log'));
    }

    /**
     * API endpoint for real-time activity logs updates.
     */
    public function getActivityLogs(Request $request)
    {
        $query = ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by user if specified
        if ($request->filled('user_filter')) {
            $query->where('user_id', $request->user_filter);
        }



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

        $logs = $query->paginate(50);
        
        // Generate HTML for table rows
        $html = '';
        foreach ($logs as $log) {
            $html .= '<tr>';
            // Date & Time column (formatted with Asia/Manila timezone)
            $html .= '<td>' . $log->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i:s A') . '</td>';
            // User column
            $html .= '<td>' . ($log->user ? $log->user->name : 'System') . '</td>';
            // User Type column with role-badge structure
            $html .= '<td>';
            if ($log->user) {
                $html .= '<span class="role-badge role-' . strtolower($log->user->role) . '">';
                $html .= strtoupper($log->user->role);
                $html .= '</span>';
            } else {
                $html .= '<span class="role-badge">SYSTEM</span>';
            }
            $html .= '</td>';
            // Action column with action-badge classes
            $html .= '<td>';
            $html .= '<span class="action-badge action-' . $log->action . '">';
            $html .= strtoupper($log->action);
            $html .= '</span>';
            $html .= '</td>';
            // Description column
            $html .= '<td>' . ($log->description ?? '') . '</td>';
            // Actions column with View Details button
            $html .= '<td>';
            $html .= '<button class="btn btn-sm btn-info view-details-btn" data-id="' . $log->id . '" onclick="showPasswordModalForDetails(' . $log->id . ')">';
            $html .= 'View Details';
            $html .= '</button>';
            $html .= '</td>';
            $html .= '</tr>';
        }
        
        // Generate pagination HTML
        $paginationHtml = $logs->appends(request()->query())->links()->render();
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'pagination' => $paginationHtml,
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
                'from' => $logs->firstItem(),
                'to' => $logs->lastItem()
            ]
        ]);
    }

    /**
     * Log when a user shows IP addresses in the activity log.
     */
    public function logIpShow(Request $request)
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'view',
            'description' => 'Viewed IP addresses in activity log',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Get recent profile updates for notifications
     */
    public function getProfileUpdates()
    {
        $updates = ActivityLog::with('user')
            ->where(function($query) {
                $query->where('action', 'update')
                      ->orWhere('action', 'profile_updated')
                      ->orWhere('action', 'PROFILE_UPDATED')
                      ->orWhere('action', 'alumni_update')
                      ->orWhere('action', 'ALUMNI_UPDATE');
            })
            ->where(function($query) {
                $query->where('description', 'like', '%profile%')
                      ->orWhere('description', 'like', '%Profile%')
                      ->orWhere('description', 'like', '%alumni record%')
                      ->orWhere('description', 'like', '%Profile updated%')
                      ->orWhere('description', 'like', '%User updated their profile%')
                      ->orWhere('description', 'like', '%successfully%');
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function($log) {
                return [
                    'user_name' => $log->user ? $log->user->name : 'Unknown User',
                    'user_email' => $log->user ? $log->user->email : 'Unknown Email',
                    'description' => $log->description,
                    'updated_at' => $log->created_at->diffForHumans(),
                ];
            });

        return response()->json(['updates' => $updates]);
    }

    /**
     * Get count of recent profile updates for notifications
     */
    public function getProfileUpdatesCount()
    {
        // Debug: Log the request
        \Log::info('getProfileUpdatesCount called');
        
        // notif bell timeout
        if (session('notifications_viewed')) {
            return response()->json(['count' => 0]);
        }
        
        $count = ActivityLog::where(function($query) {
                $query->where('action', 'update')
                      ->orWhere('action', 'profile_updated')
                      ->orWhere('action', 'PROFILE_UPDATED')
                      ->orWhere('action', 'alumni_update')
                      ->orWhere('action', 'ALUMNI_UPDATE');
            })
            ->where(function($query) {
                $query->where('description', 'like', '%profile%')
                      ->orWhere('description', 'like', '%Profile%')
                      ->orWhere('description', 'like', '%alumni record%')
                      ->orWhere('description', 'like', '%Profile updated%')
                      ->orWhere('description', 'like', '%User updated their profile%')
                      ->orWhere('description', 'like', '%successfully%');
            })
            ->count();

        // Debug: Log the count
        \Log::info('Profile updates count: ' . $count);

        return response()->json(['count' => $count]);
    }

    /**
     * Mark profile update notifications as read (simple session-based)
     */
    public function markProfileUpdatesAsRead()
    {
        // Simply set a session flag that notifications have been viewed
        session(['notifications_viewed' => true]);
        
        return response()->json(['success' => true]);
    }

    /**
     * Log when a user hides IP addresses in the activity log.
     */
    public function logIpHide(Request $request)
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'hide',
            'description' => 'Hid IP addresses in activity log',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * SuperAdmin Security Logs
     */
    public function securityLogs()
    {
        // Only SuperAdmin can access this
        if (auth()->user()->role !== 'SuperAdmin') {
            abort(403, 'Unauthorized access. SuperAdmin privileges required.');
        }

        $securityLogs = ActivityLog::where('action', 'like', '%security%')
            ->orWhere('action', 'like', '%login%')
            ->orWhere('action', 'like', '%unauthorized%')
            ->orWhere('action', 'like', '%suspicious%')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('superadmin.security.logs', compact('securityLogs'));
    }

    /**
     * SuperAdmin Security Events
     */
    public function securityEvents()
    {
        // Only SuperAdmin can access this
        if (auth()->user()->role !== 'SuperAdmin') {
            abort(403, 'Unauthorized access. SuperAdmin privileges required.');
        }

        $securityEvents = ActivityLog::where('action', 'like', '%SuperAdmin%')
            ->orWhere('action', 'like', '%security%')
            ->orWhere('action', 'like', '%failed%')
            ->orWhere('action', 'like', '%blocked%')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $eventStats = [
            'total_events' => ActivityLog::where('action', 'like', '%security%')->count(),
            'events_today' => ActivityLog::where('action', 'like', '%security%')
                ->whereDate('created_at', today())
                ->count(),
            'failed_logins_today' => ActivityLog::where('action', 'login_failed')
                ->whereDate('created_at', today())
                ->count(),
            'suspicious_activities' => ActivityLog::where('action', 'like', '%suspicious%')
                ->where('created_at', '>=', now()->subDays(7))
                ->count()
        ];

        return view('superadmin.security.events', compact('securityEvents', 'eventStats'));
    }
}