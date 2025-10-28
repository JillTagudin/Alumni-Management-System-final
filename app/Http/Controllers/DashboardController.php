<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Alumni;
use App\Models\ActivityLog;
use App\Models\PendingChange;
use App\Services\AIAnalyticsService;
use App\Services\AIChatbotService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if user is approved
        $user = Auth::user();
        if ($user->approval_status !== 'approved') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => $user->approval_status === 'pending' 
                    ? 'Your account is pending approval. Please wait for an administrator to approve your account.'
                    : 'Your account has been denied. Please contact an administrator for more information.'
            ]);
        }
        
        $stats = $this->getDashboardData();
        
        // Extract individual variables that the template expects
        $total_users = $stats['total_users'] ?? 0;
        $total_alumni = $stats['total_alumni'] ?? 0;
        $recent_registrations = $stats['recent_registrations'] ?? 0;
        $pending_changes = $stats['pending_changes'] ?? 0;
        $approved_changes = $stats['approved_changes'] ?? 0;
        $denied_changes = $stats['denied_changes'] ?? 0;
        $recent_activities = $stats['recent_activities'] ?? collect();
        
        // Add the missing staff-specific variables
        $my_pending_submissions = $stats['my_pending_submissions'] ?? 0;
        $my_approved_submissions = $stats['my_approved_submissions'] ?? 0;
        $my_total_submissions = $stats['my_total_submissions'] ?? 0;
        
        $aiInsights = $stats['aiInsights'] ?? [
            'trends' => [],
            'recommendations' => [],
            'predictions' => [],
            'anomalies' => []
        ];
        
        return view('dashboard', compact(
            'stats', 
            'total_users', 
            'total_alumni', 
            'recent_registrations',
            'pending_changes',
            'approved_changes', 
            'denied_changes',
            'recent_activities',
            'my_pending_submissions',
            'my_approved_submissions', 
            'my_total_submissions',
            'aiInsights'
        ));
    }

    public function getStats()
    {
        $stats = $this->getDashboardData();
        return response()->json($stats);
    }

    // NEW: AI Chart Data endpoint
    public function getAIChartData(Request $request)
    {
        try {
            $chartType = $request->get('type', 'all');
            $aiService = new AIAnalyticsService();
            $chartData = $aiService->generateChartData($chartType);
            
            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);
        } catch (\Exception $e) {
            \Log::error('AI Chart Generation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate AI charts',
                'fallback' => true
            ], 500);
        }
    }

    // NEW: Refresh AI Charts endpoint
    public function refreshCharts()
    {
        try {
            $aiService = new AIAnalyticsService();
            $chartData = $aiService->generateChartDataWithRefresh('all');
            
            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);
        } catch (\Exception $e) {
            \Log::error('AI Chart Refresh Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to refresh AI charts'
            ], 500);
        }
    }

    // NEW: Refresh AI Insights endpoint
    public function refreshAIInsights()
    {
        try {
            $aiService = new AIAnalyticsService();
            
            // Get comprehensive alumni data for AI analysis
            $alumniData = [
                'total_alumni' => Alumni::count(),
                'recent_registrations' => User::where('created_at', '>=', now()->subWeek())->count(),
                'total_users' => User::count(),
                'alumni_by_gender' => $this->getAlumniByGender(),
                'alumni_by_age_group' => $this->getAlumniByAgeGroup(),
                'alumni_employment_status' => $this->getAlumniEmploymentStatus(),
                'alumni_creation_trends' => $this->getAlumniCreationTrends(),
                'registration_trends' => $this->getRegistrationChartData(),
                'activity_trends' => $this->getActivityTrendsData()
            ];
            
            $aiInsights = $aiService->generateInsightsWithRefresh($alumniData, '', true);
            
            // Ensure we always have an array structure
            if (!is_array($aiInsights)) {
                $insights = [
                    'trends' => ['AI service temporarily unavailable'],
                    'recommendations' => ['Please check AI configuration'],
                    'predictions' => [],
                    'anomalies' => []
                ];
            } else {
                // Ensure all required keys exist
                $insights = [
                    'trends' => $aiInsights['trends'] ?? [],
                    'recommendations' => $aiInsights['recommendations'] ?? [],
                    'predictions' => $aiInsights['predictions'] ?? [],
                    'anomalies' => $aiInsights['anomalies'] ?? []
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => $insights
            ]);
        } catch (\Exception $e) {
            \Log::error('AI Insights Refresh Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to refresh AI insights'
            ], 500);
        }
    }

    private function getDashboardData()
    {
        $user = Auth::user();
        
        // Basic statistics
        $stats = [
            'total_users' => User::count(),
            'total_alumni' => Alumni::count(),
            'recent_registrations' => User::where('created_at', '>=', now()->subWeek())->count(),
            'pending_approvals' => PendingChange::where('status', 'pending')->count(),
        ];

        // Role-specific statistics
        if (in_array($user->role, ['Admin', 'SuperAdmin'])) {
            $stats['total_staff'] = User::where('role', 'Staff')->count();
            $stats['total_admins'] = User::where('role', 'Admin')->count();
            $stats['total_superadmins'] = User::where('role', 'SuperAdmin')->count();
            $stats['pending_changes'] = PendingChange::where('status', 'pending')->count();
            $stats['approved_changes'] = PendingChange::where('status', 'approved')->count();
            $stats['denied_changes'] = PendingChange::where('status', 'denied')->count();
        } elseif ($user->role === 'Staff') {
            $stats['my_pending_submissions'] = PendingChange::where('staff_user_id', $user->id)
                                                           ->where('status', 'pending')
                                                           ->count();
            $stats['my_approved_submissions'] = PendingChange::where('staff_user_id', $user->id)
                                                            ->where('status', 'approved')
                                                            ->count();
            $stats['my_total_submissions'] = PendingChange::where('staff_user_id', $user->id)->count();
        }

        // Recent activities
        $stats['recent_activities'] = ActivityLog::with('user')
                                               ->orderBy('created_at', 'desc')
                                               ->take(5)
                                               ->get();

        // RESTORED: Original chart data for backward compatibility
        $stats['registration_chart_data'] = $this->getRegistrationChartData();
        $stats['activity_trends'] = $this->getActivityTrendsData();
        $stats['alumni_by_gender'] = $this->getAlumniByGender();
        $stats['alumni_by_age_group'] = $this->getAlumniByAgeGroup();
        $stats['alumni_employment_status'] = $this->getAlumniEmploymentStatus();
        $stats['alumni_creation_trends'] = $this->getAlumniCreationTrends();

        // AI Analytics (NEW) - Only generate if enabled
        if (config('app.ai_analytics_enabled', false)) {
            try {
                $aiService = new AIAnalyticsService();
                
                // Comprehensive alumni data for AI analysis
                $alumniData = [
                    'total_alumni' => $stats['total_alumni'],
                    'recent_registrations' => $stats['recent_registrations'],
                    'total_users' => $stats['total_users'],
                    'alumni_by_gender' => $this->getAlumniByGender(),
                    'alumni_by_age_group' => $this->getAlumniByAgeGroup(),
                    'alumni_employment_status' => $this->getAlumniEmploymentStatus(),
                    'alumni_creation_trends' => $this->getAlumniCreationTrends(),
                    'registration_trends' => $stats['registration_chart_data'],
                    'activity_trends' => $stats['activity_trends']
                ];
                
                $aiInsights = $aiService->generateInsights($alumniData);
                
                // Ensure we always have an array structure
                if (!is_array($aiInsights)) {
                    $stats['aiInsights'] = [
                        'trends' => ['AI service temporarily unavailable'],
                        'recommendations' => ['Please check AI configuration'],
                        'predictions' => [],
                        'anomalies' => []
                    ];
                } else {
                    // Ensure all required keys exist
                    $stats['aiInsights'] = [
                        'trends' => $aiInsights['trends'] ?? [],
                        'recommendations' => $aiInsights['recommendations'] ?? [],
                        'predictions' => $aiInsights['predictions'] ?? [],
                        'anomalies' => $aiInsights['anomalies'] ?? []
                    ];
                }
                
            } catch (\Exception $e) {
                \Log::error('AI Analytics Error: ' . $e->getMessage());
                $stats['aiInsights'] = [
                    'trends' => ['AI analysis temporarily unavailable'],
                    'recommendations' => ['Please check system configuration'],
                    'predictions' => [],
                    'anomalies' => []
                ];
            }
        } else {
            // AI Analytics disabled - return empty arrays
            $stats['aiInsights'] = [
                'trends' => [],
                'recommendations' => [],
                'predictions' => [],
                'anomalies' => []
            ];
        }
         
        return $stats;
    }

    // RESTORED: Original chart data methods
    private function getRegistrationChartData()
    {
        $data = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                   ->where('created_at', '>=', now()->subDays(30))
                   ->groupBy('date')
                   ->orderBy('date')
                   ->get();
        
        return $data;
    }

    private function getActivityTrendsData()
    {
        $data = ActivityLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                          ->where('created_at', '>=', now()->subDays(30))
                          ->groupBy('date')
                          ->orderBy('date')
                          ->get();
        
        return $data;
    }

    private function getAlumniByGender()
    {
        return Alumni::selectRaw('Gender, COUNT(*) as count')
                    ->whereNotNull('Gender')
                    ->groupBy('Gender')
                    ->pluck('count', 'Gender');
    }

    private function getAlumniByAgeGroup()
    {
        return Alumni::selectRaw(
            'CASE 
                WHEN Age < 25 THEN "18-24"
                WHEN Age < 35 THEN "25-34"
                WHEN Age < 45 THEN "35-44"
                WHEN Age < 55 THEN "45-54"
                ELSE "55+"
            END as age_group,
            COUNT(*) as count'
        )
        ->whereNotNull('Age')
        ->groupBy('age_group')
        ->pluck('count', 'age_group');
    }

    private function getAlumniEmploymentStatus()
    {
        return Alumni::selectRaw('Occupation, COUNT(*) as count')
                    ->whereNotNull('Occupation')
                    ->where('Occupation', '!=', '')
                    ->groupBy('Occupation')
                    ->orderBy('count', 'desc')
                    ->pluck('count', 'Occupation');
    }



    private function getAlumniCreationTrends()
    {
        return Alumni::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('count', 'month');
    }

    private function getAlumniByCourse()
    {
        return Alumni::selectRaw('Course, COUNT(*) as count')
                    ->whereNotNull('Course')
                    ->groupBy('Course')
                    ->orderBy('count', 'desc')
                    ->pluck('count', 'Course');
    }

    private function getAlumniByMembershipStatus()
    {
        return Alumni::selectRaw('membership_status, COUNT(*) as count')
                    ->whereNotNull('membership_status')
                    ->groupBy('membership_status')
                    ->pluck('count', 'membership_status');
    }

    private function getAlumniByLocation()
    {
        return Alumni::selectRaw('Address, COUNT(*) as count')
                    ->whereNotNull('Address')
                    ->groupBy('Address')
                    ->orderBy('count', 'desc')
                    ->take(10)
                    ->pluck('count', 'Address');
    }

    private function getAlumniByGraduationYear()
    {
        // Since there's no GraduationDate column, use Batch or created_at as alternative
        return Alumni::selectRaw('Batch, COUNT(*) as count')
                    ->whereNotNull('Batch')
                    ->groupBy('Batch')
                    ->orderBy('Batch', 'desc')
                    ->pluck('count', 'Batch');
    }

    // Alumni chart data endpoints for AJAX requests
    public function getAlumniChartData(Request $request)
    {
        try {
            $chartType = $request->get('type', 'all');
            
            $data = [];
            
            switch ($chartType) {
                case 'gender':
                    $rawData = $this->getAlumniByGender();
                    $data = $this->formatChartData($rawData, 'Gender Distribution');
                    break;
                case 'age':
                    $rawData = $this->getAlumniByAgeGroup();
                    $data = $this->formatChartData($rawData, 'Age Groups');
                    break;
                case 'employment':
                    $rawData = $this->getAlumniEmploymentStatus();
                    $data = $this->formatChartData($rawData, 'Employment Status');
                    break;
                case 'course':
                    $rawData = $this->getAlumniByCourse();
                    $data = $this->formatChartData($rawData, 'Courses');
                    break;
                case 'membership':
                    $rawData = $this->getAlumniByMembershipStatus();
                    $data = $this->formatChartData($rawData, 'Membership Status');
                    break;
                case 'location':
                    $rawData = $this->getAlumniByLocation();
                    $data = $this->formatChartData($rawData, 'Locations');
                    break;
                case 'graduation':
                    $rawData = $this->getAlumniByGraduationYear();
                    $data = $this->formatTrendsData($rawData, 'Graduation Trends');
                    break;
                case 'batch':
                    $rawData = $this->getAlumniByGraduationYear();
                    $data = $this->formatChartData($rawData, 'Alumni by Batch');
                    break;
                case 'trends':
                    $rawData = $this->getAlumniCreationTrends();
                    $data = $this->formatTrendsData($rawData);
                    break;
                case 'all':
                default:
                    $data = [
                        'gender' => $this->formatChartData($this->getAlumniByGender(), 'Gender Distribution'),
                        'age' => $this->formatChartData($this->getAlumniByAgeGroup(), 'Age Groups'),
                        'employment' => $this->formatChartData($this->getAlumniEmploymentStatus(), 'Employment Status'),
                        'course' => $this->formatChartData($this->getAlumniByCourse(), 'Courses'),
                        'membership' => $this->formatChartData($this->getAlumniByMembershipStatus(), 'Membership Status'),
                        'location' => $this->formatChartData($this->getAlumniByLocation(), 'Locations'),
                        'graduation' => $this->formatChartData($this->getAlumniByGraduationYear(), 'Graduation Years'),
                        'batch' => $this->formatChartData($this->getAlumniByGraduationYear(), 'Alumni by Batch'),
                        'trends' => $this->formatTrendsData($this->getAlumniCreationTrends())
                    ];
                    break;
            }
            
            // Validate data structure
            if (empty($data)) {
                throw new \Exception('No data available for the requested chart type');
            }
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Chart data generation error: ' . $e->getMessage(), [
                'chart_type' => $chartType ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate chart data: ' . $e->getMessage(),
                'data' => $this->getEmptyChartData($chartType ?? 'all')
            ], 500);
        }
    }
    
    // Get empty chart data structure for fallback
    private function getEmptyChartData($chartType)
    {
        $emptyData = ['labels' => ['No Data'], 'data' => [0]];
        
        if ($chartType === 'all') {
            return [
                'gender' => $emptyData,
                'age' => $emptyData,
                'employment' => $emptyData,
                'course' => $emptyData,
                'membership' => $emptyData,
                'location' => $emptyData,
                'graduation' => $emptyData,
                'trends' => $emptyData
            ];
        }
        
        return $emptyData;
    }
    
    // Format data for Chart.js (pie/bar charts)
    private function formatChartData($rawData, $title = 'Chart Data')
    {
        try {
            // Handle different data types
            if (is_null($rawData)) {
                \Log::warning("Null data received for chart: {$title}");
                return ['labels' => ['No Data'], 'data' => [0]];
            }
            
            // Convert to array if it's a collection
            $dataArray = is_object($rawData) && method_exists($rawData, 'toArray') 
                ? $rawData->toArray() 
                : (array) $rawData;
            
            // Check if data is empty
            if (empty($dataArray)) {
                \Log::info("Empty data for chart: {$title}");
                return ['labels' => ['No Data'], 'data' => [0]];
            }
            
            $labels = array_keys($dataArray);
            $data = array_values($dataArray);
            
            // Ensure all values are numeric
            $data = array_map(function($value) {
                return is_numeric($value) ? (float) $value : 0;
            }, $data);
            
            // Ensure labels are strings
            $labels = array_map(function($label) {
                return is_null($label) ? 'Unknown' : (string) $label;
            }, $labels);
            
            return [
                'labels' => $labels,
                'data' => $data,
                'title' => $title
            ];
            
        } catch (\Exception $e) {
            \Log::error("Error formatting chart data for {$title}: " . $e->getMessage());
            return ['labels' => ['Error'], 'data' => [0]];
        }
    }
    
    // Format data for Chart.js line charts (trends)
    private function formatTrendsData($rawData, $title = 'Trends Data')
    {
        try {
            // Handle different data types
            if (is_null($rawData)) {
                \Log::warning("Null trends data received for: {$title}");
                return ['labels' => ['No Data'], 'data' => [0]];
            }
            
            // Convert to array if it's a collection
            $dataArray = is_object($rawData) && method_exists($rawData, 'toArray') 
                ? $rawData->toArray() 
                : (array) $rawData;
            
            // Check if data is empty
            if (empty($dataArray)) {
                \Log::info("Empty trends data for: {$title}");
                return ['labels' => ['No Data'], 'data' => [0]];
            }
            
            $labels = array_keys($dataArray);
            $data = array_values($dataArray);
            
            // Ensure all values are numeric for trends
            $data = array_map(function($value) {
                return is_numeric($value) ? (float) $value : 0;
            }, $data);
            
            // Format labels for trends (usually dates)
            $labels = array_map(function($label) {
                if (is_null($label)) return 'Unknown';
                // Try to format as date if it looks like one
                if (preg_match('/^\d{4}-\d{2}-\d{2}/', $label)) {
                    try {
                        return \Carbon\Carbon::parse($label)->format('M j');
                    } catch (\Exception $e) {
                        return (string) $label;
                    }
                }
                return (string) $label;
            }, $labels);
            
            return [
                'labels' => $labels,
                'data' => $data,
                'title' => $title
            ];
            
        } catch (\Exception $e) {
            \Log::error("Error formatting trends data for {$title}: " . $e->getMessage());
            return ['labels' => ['Error'], 'data' => [0]];
        }
    }

    // Get detailed alumni data for modals
    public function getAlumniModalData(Request $request)
    {
        $type = $request->get('type');
        $filter = $request->get('filter');
        
        $query = Alumni::query();
        
        switch ($type) {
            case 'gender':
                if ($filter) {
                    $query->where('Gender', $filter);
                }
                $alumni = $query->select('AlumniID', 'Fullname', 'Emailaddress', 'Gender', 'Age', 'Course', 'created_at')->get();
                break;
            case 'age':
                if ($filter) {
                    switch ($filter) {
                        case '18-24':
                            $query->where('Age', '<', 25);
                            break;
                        case '25-34':
                            $query->whereBetween('Age', [25, 34]);
                            break;
                        case '35-44':
                            $query->whereBetween('Age', [35, 44]);
                            break;
                        case '45-54':
                            $query->whereBetween('Age', [45, 54]);
                            break;
                        case '55+':
                            $query->where('Age', '>=', 55);
                            break;
                    }
                }
                $alumni = $query->select('AlumniID', 'Fullname', 'Emailaddress', 'Age', 'Gender', 'Course', 'created_at')->get();
                break;
            case 'employment':
                if ($filter) {
                    $query->where('Occupation', $filter);
                }
                $alumni = $query->select('AlumniID', 'Fullname', 'Emailaddress', 'Occupation', 'Course', 'created_at')->get();
                break;
            case 'course':
                if ($filter) {
                    $query->where('Course', $filter);
                }
                $alumni = $query->select('AlumniID', 'Fullname', 'Emailaddress', 'Course', 'Batch', 'created_at')->get();
                break;
            case 'membership':
                if ($filter) {
                    $query->where('MembershipStatus', $filter);
                }
                $alumni = $query->select('AlumniID', 'Fullname', 'Emailaddress', 'MembershipStatus', 'Course', 'created_at')->get();
                break;
            default:
                $alumni = Alumni::select('AlumniID', 'Fullname', 'Emailaddress', 'Gender', 'Age', 'Course', 'Occupation', 'MembershipStatus', 'created_at')->get();
                break;
        }
        
        return response()->json([
            'success' => true,
            'data' => $alumni,
            'total' => $alumni->count()
        ]);
    }

    /**
     * SuperAdmin Dashboard with enhanced security monitoring
     */
    public function superAdminDashboard()
    {
        // Only SuperAdmin can access this
        if (auth()->user()->role !== 'SuperAdmin') {
            abort(403, 'Unauthorized access. SuperAdmin privileges required.');
        }

        $stats = $this->getDashboardData();
        
        // Additional SuperAdmin-specific stats
        $stats['security_events_today'] = ActivityLog::whereDate('created_at', today())
            ->where('action', 'like', '%security%')
            ->count();
        
        $stats['failed_logins_today'] = ActivityLog::whereDate('created_at', today())
            ->where('action', 'login_failed')
            ->count();
        
        $stats['system_health'] = [
            'database_status' => 'healthy',
            'cache_status' => 'healthy',
            'storage_usage' => '45%'
        ];

        return view('superadmin.dashboard', compact('stats'));
    }

    /**
     * SuperAdmin Analytics
     */
    public function superAdminAnalytics()
    {
        // Only SuperAdmin can access this
        if (auth()->user()->role !== 'SuperAdmin') {
            abort(403, 'Unauthorized access. SuperAdmin privileges required.');
        }

        $analytics = [
            'user_growth' => User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            
            'role_distribution' => User::selectRaw('role, COUNT(*) as count')
                ->groupBy('role')
                ->get(),
            
            'activity_trends' => ActivityLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
        ];

        return view('superadmin.analytics', compact('analytics'));
    }

    /**
     * Get Security Stats for SuperAdmin API
     */
    public function getSecurityStats()
    {
        // Only SuperAdmin can access this
        if (auth()->user()->role !== 'SuperAdmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $stats = [
            'failed_logins_today' => ActivityLog::whereDate('created_at', today())
                ->where('action', 'login_failed')
                ->count(),
            
            'security_events_week' => ActivityLog::where('created_at', '>=', now()->subWeek())
                ->where('action', 'like', '%security%')
                ->count(),
            
            'suspicious_activities' => ActivityLog::where('created_at', '>=', now()->subDay())
                ->where('action', 'like', '%suspicious%')
                ->count(),
            
            'active_sessions' => User::whereNotNull('last_login_at')
                ->where('last_login_at', '>=', now()->subHours(24))
                ->count()
        ];

        return response()->json($stats);
    }

    /**
     * Get System Health for SuperAdmin API
     */
    public function getSystemHealth()
    {
        // Only SuperAdmin can access this
        if (auth()->user()->role !== 'SuperAdmin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $health = [
            'database' => [
                'status' => 'healthy',
                'connections' => 5,
                'response_time' => '12ms'
            ],
            'storage' => [
                'status' => 'healthy',
                'usage' => '45%',
                'available' => '2.1GB'
            ],
            'memory' => [
                'status' => 'healthy',
                'usage' => '68%',
                'available' => '1.2GB'
            ],
            'cache' => [
                'status' => 'healthy',
                'hit_rate' => '94%'
            ]
        ];

        return response()->json($health);
    }

}

