<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\Auth\UserRegisterController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\AccountManagementController; 
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\DashboardController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnnouncementController; 
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\BalanceUpdateController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\AlumniConcernController;
use App\Http\Controllers\AdminHRController;
use App\Http\Controllers\HRController; // Add this missing import
use App\Http\Controllers\JobOpportunityController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\WelcomeController;


// Root route - show welcome page
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Public announcement route for social sharing (no authentication required)
Route::get('/announcements/{announcement}', [AnnouncementController::class, 'showPublic'])->name('announcements.public');

// API routes for registration (public)
Route::post('/api/check-email', [App\Http\Controllers\Api\EmailCheckController::class, 'checkEmail']);



// Profile Routes - Accessible by authenticated users
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Password verification route for sensitive data access
    Route::post('/verify-password', [ConfirmablePasswordController::class, 'verify'])->name('verify.password');
});

// Admin Routes - Accessible by Admin, Staff, and SuperAdmin roles
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    // Main Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard API endpoints - Admin/Staff only
    Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/api/dashboard/alumni-chart-data', [DashboardController::class, 'getAlumniChartData'])->name('dashboard.alumni-chart-data');
    Route::get('/api/dashboard/alumni-modal-data', [DashboardController::class, 'getAlumniModalData'])->name('dashboard.alumni-modal-data');
    
    // Chart Data API Endpoints - Admin/Staff only
    Route::get('/api/dashboard/user-stats-chart', [DashboardController::class, 'getUserStatsChart'])->name('dashboard.user-stats-chart');
    Route::get('/api/dashboard/approval-workflow-chart', [DashboardController::class, 'getApprovalWorkflowChart'])->name('dashboard.approval-workflow-chart');
    Route::get('/api/dashboard/activity-analytics-chart', [DashboardController::class, 'getActivityAnalyticsChart'])->name('dashboard.activity-analytics-chart');
    Route::get('/api/dashboard/role-based-chart', [DashboardController::class, 'getRoleBasedChart'])->name('dashboard.role-based-chart');
    Route::get('/api/dashboard/registration-trends-chart', [DashboardController::class, 'getRegistrationTrendsChart'])->name('dashboard.registration-trends-chart');
    
   
    // Alumni Tracer Study Analytics Routes - Admin/Staff only
    Route::get('/api/dashboard/alumni-tracer-study-chart', [DashboardController::class, 'getAlumniTracerStudyChart'])->name('dashboard.alumni-tracer-study-chart');
    Route::get('/api/dashboard/alumni-demographics-chart', [DashboardController::class, 'getAlumniDemographicsChart'])->name('dashboard.alumni-demographics-chart');
    Route::get('/api/dashboard/alumni-employment-chart', [DashboardController::class, 'getAlumniEmploymentChart'])->name('dashboard.alumni-employment-chart');
    
    // AI Analytics Routes - Admin/Staff only
    Route::get('/api/dashboard/refresh-ai-insights', [DashboardController::class, 'refreshAIInsights'])->name('dashboard.refresh-ai-insights');
    Route::get('/api/dashboard/ai-chart-data', [DashboardController::class, 'getAIChartData'])->name('dashboard.ai-chart-data');
    Route::get('/api/dashboard/refresh-charts', [DashboardController::class, 'refreshCharts'])->name('dashboard.refresh-charts');
    Route::get('/dashboard/ai-charts', [DashboardController::class, 'getAIChartData'])->name('dashboard.ai-charts');
    Route::post('/dashboard/refresh-charts', [DashboardController::class, 'refreshCharts'])->name('dashboard.refresh-charts');
    Route::post('/dashboard/refresh-ai-insights', [DashboardController::class, 'refreshAIInsights'])->name('dashboard.refresh-ai');
    
    // Announcement Management Routes - Admin/Staff only
    Route::get('/announcement', function () {
        return view('announcement');
    })->name('announcement');
    Route::get('/admin/announcement', [AnnouncementController::class, 'create'])->name('admin.announcement');
    Route::post('/admin/announcement', [AnnouncementController::class, 'store'])->name('admin.announcement.store');
    Route::get('/Module 2', function () {
        return view('dashboard');
    })->name('Module 2');
    
    // Alumni Management Routes - Admin/Staff only
    Route::get('/Alumni', [AlumniController::class, 'index'])->name('Alumni.index');
    Route::get('/Alumni/create', [AlumniController::class, 'create'])->name('Alumni.create');
    Route::post('/Alumni', [AlumniController::class, 'store'])->name('Alumni.store');
    Route::get('/Alumni/{id}/edit', [AlumniController::class, 'edit'])->name('Alumni.edit');
    Route::put('/Alumni/{id}', [AlumniController::class, 'update'])->name('Alumni.update');
    Route::delete('/Alumni/{id}', [AlumniController::class, 'destroy'])->name('Alumni.destroy');
    
    // Account Management Routes - Admin/Staff only
    Route::get('/AccountManagement', [AccountManagementController::class, 'index'])->name('AccountManagement.index');
    Route::patch('/AccountManagement/{user}/role', [AccountManagementController::class, 'updateRole'])->name('AccountManagement.updateRole');
    
    // Activity Logs Routes - Admin/Staff only
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/{log}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('/activity-logs/{log}/details', [ActivityLogController::class, 'show'])->name('activity-logs.details');
    Route::get('/api/activity-logs', [ActivityLogController::class, 'getActivityLogs'])->name('activity-logs.api');
    
    // Profile update notification API routes
    Route::get('/api/profile-updates', [ActivityLogController::class, 'getProfileUpdates'])->name('api.profile-updates');
    Route::get('/api/profile-updates-count', [ActivityLogController::class, 'getProfileUpdatesCount'])->name('api.profile-updates-count');
    Route::post('/api/profile-updates/mark-read', [ActivityLogController::class, 'markProfileUpdatesAsRead'])->name('api.profile-updates.mark-read');
    
    // Sendmail Routes - Admin/Staff only
    Route::get('/Sendmail', function () {
        return view('Sendmail');
    })->name('Sendmail');
    Route::post('/send-mail', [SendMailController::class, 'send'])->name('send-mail');
    
    // Feedback Management Routes - Admin/Staff only
    Route::get('/admin/feedback', [FeedbackController::class, 'index'])->name('admin.feedback.index');
    Route::get('/admin/feedback/{feedback}', [FeedbackController::class, 'show'])->name('admin.feedback.show');
    Route::patch('/admin/feedback/{feedback}', [FeedbackController::class, 'update'])->name('admin.feedback.update');
    Route::post('/admin/feedback/{feedback}/respond', [FeedbackController::class, 'respond'])->name('admin.feedback.respond');
    
    // Reports Routes - Admin/Staff only
    Route::get('/admin/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::post('/admin/reports/generate', [ReportsController::class, 'generate'])->name('reports.generate');
    Route::match(['get', 'post'], '/admin/reports/export', [ReportsController::class, 'export'])->name('reports.export');
    
    // Membership Routes - Admin/Staff only
    Route::get('/membership', [MembershipController::class, 'index'])->name('membership.index');
    Route::post('/membership/sync', [MembershipController::class, 'syncWithBalanceUpdate'])->name('membership.sync');
    Route::put('/membership/{id}', [MembershipController::class, 'updateMembership'])->name('membership.update');
    Route::patch('/membership/{id}/update', [MembershipController::class, 'updateMembership'])->name('membership.patch');
    
    // MIS Route - Admin only
    Route::get('/admin/balance-update', [BalanceUpdateController::class, 'index'])->name('admin.balanceupdate.index');
    
    // Job Opportunity Routes - Admin/Staff/HR only
    Route::get('/job-opportunity', [JobOpportunityController::class, 'create'])->name('job-opportunity');
    Route::post('/job-opportunity', [JobOpportunityController::class, 'store'])->name('job-opportunity.store');
});

// Approval Routes - Admin and SuperAdmin only
Route::middleware(['auth', 'verified', 'role:Admin,SuperAdmin'])->group(function () {
    Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
    Route::post('/approval/{pendingChange}/approve', [ApprovalController::class, 'approve'])->name('approval.approve');
    Route::post('/approval/{pendingChange}/deny', [ApprovalController::class, 'deny'])->name('approval.deny');
    Route::post('/approval/users/{user}/approve', [ApprovalController::class, 'approveUser'])->name('approval.users.approve');
    Route::post('/approval/users/{user}/deny', [ApprovalController::class, 'denyUser'])->name('approval.users.deny');
    Route::get('/api/admin-pending-count', [ApprovalController::class, 'getAdminPendingCount'])->name('api.admin-pending-count');
});

// Staff Pending Changes Routes - Staff only
Route::middleware(['auth', 'verified', 'role:Staff'])->group(function () {
    Route::get('/staff/pending-changes', [ApprovalController::class, 'staffPendingChanges'])->name('staff.pending-changes');
    Route::get('/api/staff/pending-changes', [ApprovalController::class, 'getStaffPendingChanges'])->name('staff.pending-changes.api');
    Route::get('/api/pending-counts', [ApprovalController::class, 'getPendingCounts'])->name('api.pending-counts');
});

// User Routes - Accessible by Alumni only
Route::middleware(['auth', 'verified', 'role:Alumni'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'dashboard'])->name('user.dashboard');
    
    // User announcements
    Route::get('/user/announcement', [AnnouncementController::class, 'index'])->name('user.announcement');
    Route::get('/user/announcement/{id}', [AnnouncementController::class, 'show'])->middleware('ownership:profile')->name('user.announcement.show');
    Route::post('/user/announcement/{id}/mark-read', [AnnouncementController::class, 'markAsRead'])->name('user.announcement.mark-read');
    Route::get('/api/latest-announcements', [AnnouncementController::class, 'getLatestAnnouncements'])->name('api.latest-announcements');
    
    // User profile management
    Route::get('/user/profile', [UserProfileController::class, 'edit'])->name('user.profile.edit');
    Route::patch('/user/profile', [UserProfileController::class, 'update'])->middleware('ownership:profile')->name('user.profile.update');
    Route::put('/user/profile/password', [UserProfileController::class, 'updatePassword'])->middleware('ownership:profile')->name('user.password.update');
    
    // Alumni can view/edit their own alumni record only
    Route::get('/Alumni/{id}', [AlumniController::class, 'edit'])->middleware('ownership:alumni')->name('Alumni.edit');
    Route::put('/Alumni/{id}', [AlumniController::class, 'update'])->middleware('ownership:alumni')->name('Alumni.update');
    
    // Alumni Activity Logging Routes (own records only)
    Route::post('/Alumni/{id}/log-view', [AlumniController::class, 'logView'])->middleware('ownership:alumni')->name('Alumni.log-view');
    Route::post('/Alumni/{id}/log-hide', [AlumniController::class, 'logHide'])->middleware('ownership:alumni')->name('Alumni.log-hide');
    
    // Alumni Activity Logs Routes (own logs only)
    Route::post('/activity-logs/log-ip-show', [ActivityLogController::class, 'logIpShow'])->middleware('ownership:profile')->name('activity-logs.log-ip-show');
    Route::post('/activity-logs/log-ip-hide', [ActivityLogController::class, 'logIpHide'])->middleware('ownership:profile')->name('activity-logs.log-ip-hide');
    
    // Alumni Membership Route (view only)
    Route::get('/user/membership', [MembershipController::class, 'index'])->name('user.membership');
    
    // Job Opportunity Routes - Alumni only
    Route::get('/user/job-opportunity', [JobOpportunityController::class, 'index'])->name('user.job-opportunity');
    Route::get('/user/job-opportunity/{id}', [JobOpportunityController::class, 'show'])->name('user.job-opportunity.show');
    
    // Feedback Routes - Alumni can submit and view their own feedback
    Route::get('/user/feedback', [FeedbackController::class, 'create'])->name('user.feedback.create');
    Route::post('/user/feedback', [FeedbackController::class, 'store'])->name('user.feedback.store');
    Route::get('/user/feedback/list', [FeedbackController::class, 'userFeedback'])->name('user.feedback.index');
    Route::get('/user/feedback/{feedback}', [FeedbackController::class, 'show'])->middleware('ownership:feedback')->name('user.feedback.show');
});

// Logout Route - Should be accessible to authenticated users
Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        \Illuminate\Support\Facades\Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/user-register', [UserRegisterController::class, 'create'])->name('user.register');
    Route::post('/user-register', [UserRegisterController::class, 'store']);
});

require __DIR__.'/auth.php';

// SuperAdmin Routes - Highest security level with anti-URL manipulation
Route::middleware(['auth', 'verified', 'superadmin'])->group(function () {
    // SuperAdmin Dashboard with enhanced security monitoring
    Route::get('/superadmin/dashboard', [DashboardController::class, 'superAdminDashboard'])->name('superadmin.dashboard');
    
    // Security Management Routes
    Route::get('/superadmin/security', function () {
        return view('superadmin.security');
    })->name('superadmin.security');
    
    Route::get('/superadmin/security/logs', [ActivityLogController::class, 'securityLogs'])->name('superadmin.security.logs');
    Route::get('/superadmin/security/events', [ActivityLogController::class, 'securityEvents'])->name('superadmin.security.events');
    
    // User Management with SuperAdmin privileges
    Route::get('/superadmin/users', [AccountManagementController::class, 'superAdminIndex'])->name('superadmin.users');
    Route::patch('/superadmin/users/{user}/role', [AccountManagementController::class, 'superAdminUpdateRole'])->name('superadmin.users.updateRole');
    Route::delete('/superadmin/users/{user}', [AccountManagementController::class, 'superAdminDeleteUser'])->name('superadmin.users.delete');
    
    // System Configuration Routes
    Route::get('/superadmin/config', function () {
        return view('superadmin.config');
    })->name('superadmin.config');
    
    // Advanced Analytics and Reports
    Route::get('/superadmin/analytics', [DashboardController::class, 'superAdminAnalytics'])->name('superadmin.analytics');
    Route::get('/superadmin/reports/security', [ReportsController::class, 'securityReport'])->name('superadmin.reports.security');
    Route::get('/superadmin/reports/system', [ReportsController::class, 'systemReport'])->name('superadmin.reports.system');
    
    // Database Management (read-only views)
    Route::get('/superadmin/database/status', function () {
        return view('superadmin.database.status');
    })->name('superadmin.database.status');
    
    // API endpoints for SuperAdmin
    Route::get('/api/superadmin/security-stats', [DashboardController::class, 'getSecurityStats'])->name('api.superadmin.security-stats');
    Route::get('/api/superadmin/system-health', [DashboardController::class, 'getSystemHealth'])->name('api.superadmin.system-health');
});

// Alumni Concerns Routes - User Interface (Alumni only)
Route::middleware(['auth', 'verified', 'role:Alumni'])->group(function () {
    Route::get('/user/alumni-concerns', [AlumniConcernController::class, 'userIndex'])->name('user.alumni-concerns.index');
    Route::get('/user/alumni-concerns/create', [AlumniConcernController::class, 'create'])->name('user.alumni-concerns.create');
    Route::post('/user/alumni-concerns', [AlumniConcernController::class, 'store'])->name('user.alumni-concerns.store');
    Route::get('/user/alumni-concerns/{concern}', [AlumniConcernController::class, 'show'])->middleware('ownership:concern')->name('user.alumni-concerns.show');
});

// Alumni Concerns Routes - Admin Interface (Staff/Admin/SuperAdmin only)
Route::middleware(['auth', 'verified', 'role:Staff'])->group(function () {
    Route::get('/admin/alumni-concerns', [AlumniConcernController::class, 'adminIndex'])->name('admin.alumni-concerns.index');
    Route::get('/admin/alumni-concerns/{concern}', [AlumniConcernController::class, 'adminShow'])->name('admin.alumni-concerns.show');
    Route::post('/admin/alumni-concerns/{concern}/respond', [AlumniConcernController::class, 'respond'])->name('admin.alumni-concerns.respond');
    Route::patch('/admin/alumni-concerns/{concern}/status', [AlumniConcernController::class, 'updateStatus'])->name('admin.alumni-concerns.update-status');
});

// HR Routes
Route::middleware(['auth', 'role:HR'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/dashboard', [HRController::class, 'dashboard'])->name('dashboard');
    Route::get('/announcement/create', [AnnouncementController::class, 'create'])->name('announcement.create');
    Route::post('/announcement', [AnnouncementController::class, 'store'])->name('announcement.store');
    Route::get('/announcement/pending', [HRController::class, 'pendingAnnouncements'])->name('announcement.pending');
    Route::get('/announcement/{announcement}/edit', [HRController::class, 'editAnnouncement'])->name('announcement.edit');
    
    // HR-specific announcement viewing routes
    Route::get('/announcements', [HRController::class, 'announcements'])->name('announcements');
    Route::get('/announcements/{id}', [HRController::class, 'showAnnouncement'])->name('announcement.show');
    Route::post('/announcements/{id}/mark-read', [HRController::class, 'markAsRead'])->name('announcement.mark-read');
    
    // HR Job Opportunity Routes
    Route::get('/job-opportunity', [JobOpportunityController::class, 'create'])->name('job-opportunity');
    Route::post('/job-opportunity', [JobOpportunityController::class, 'store'])->name('job-opportunity.store');
    Route::get('/job-posting/pending', [HRController::class, 'pendingJobPostings'])->name('job-posting.pending');
    
    // HR-specific job opportunity viewing routes
    Route::get('/job-opportunities', [HRController::class, 'jobOpportunities'])->name('job-opportunities');
    Route::get('/job-opportunities/{id}', [HRController::class, 'showJobOpportunity'])->name('job-opportunity.show');
});

// Admin HR Approval Routes - Protected by Admin role middleware
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/hr-approvals', [AdminHRController::class, 'hrApprovals'])->name('hr-approvals');
    Route::post('/hr-announcement/{announcement}/approve', [AdminHRController::class, 'approveHRAnnouncement'])->name('hr-announcement.approve');
    Route::post('/hr-announcement/{announcement}/reject', [AdminHRController::class, 'rejectHRAnnouncement'])->name('hr-announcement.reject');
});

// Keep existing routes for Alumni and Admin/Staff
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/announcement', [AnnouncementController::class, 'index'])->name('announcement');
    Route::get('/announcement/{announcement}', [AnnouncementController::class, 'show'])->name('announcement.show');
    Route::post('/announcement/{announcement}/mark-read', [AnnouncementController::class, 'markAsRead'])->name('announcement.mark-read');
});

Route::middleware(['auth', 'role:Staff,Admin,HR'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/announcement/create', [AnnouncementController::class, 'create'])->name('announcement.create');
    Route::post('/announcement', [AnnouncementController::class, 'store'])->name('announcement.store');
});

Route::middleware(['auth', 'role:Admin,Staff'])->prefix('students')->name('students.')->group(function () {
    Route::get('/', [StudentApiController::class, 'index'])->name('index');
    Route::get('/college', [StudentApiController::class, 'college'])->name('college');
    Route::get('/senior-high', [StudentApiController::class, 'seniorHigh'])->name('senior-high');
    Route::post('/sync', [StudentApiController::class, 'syncWithAlumniRecords'])->name('sync');
    Route::post('/send-invitations', [StudentApiController::class, 'sendRegistrationInvitations'])->name('send-invitations');
});