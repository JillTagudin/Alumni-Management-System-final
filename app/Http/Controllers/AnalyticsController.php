<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Alumni;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        return view('analytics.index', [
            'user_growth' => $this->getUserGrowthData(),
            'activity_heatmap' => $this->getActivityHeatmap(),
            'top_actions' => $this->getTopActions(),
            'geographic_data' => $this->getGeographicData(),
            'engagement_metrics' => $this->getEngagementMetrics()
        ]);
    }
    
    private function getUserGrowthData()
    {
        return User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as daily_count'),
            DB::raw('SUM(COUNT(*)) OVER (ORDER BY DATE(created_at)) as cumulative_count')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('date')
        ->orderBy('date')
        ->get();
    }
    
    private function getActivityHeatmap()
    {
        return ActivityLog::select(
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('DAYOFWEEK(created_at) - 1 as day_of_week'),
            DB::raw('COUNT(*) as activity_count')
        )
        ->where('created_at', '>=', now()->subMonths(1))
        ->groupBy('hour', 'day_of_week')
        ->get();
    }
    
    private function getTopActions()
    {
        return ActivityLog::select('action', DB::raw('COUNT(*) as count'))
                         ->groupBy('action')
                         ->orderBy('count', 'desc')
                         ->take(10)
                         ->get();
    }
    
    private function getEngagementMetrics()
    {
        $totalUsers = User::count();
        $activeUsers = ActivityLog::distinct('user_id')
                                 ->where('created_at', '>=', now()->subDays(30))
                                 ->count();
        
        return [
            'total_users' => $totalUsers,
            'active_users_30d' => $activeUsers,
            'engagement_rate' => $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 2) : 0,
            'avg_sessions_per_user' => $this->getAverageSessionsPerUser(),
            'retention_rate' => $this->getRetentionRate()
        ];
    }
}