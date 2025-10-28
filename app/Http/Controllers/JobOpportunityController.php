<?php

namespace App\Http\Controllers;

use App\Models\JobOpportunity;
use App\Models\PendingChange;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JobOpportunityController extends Controller
{
    public function create()
    {
        return view('job-opportunity');
    }

    public function index(Request $request)
    {
        $query = JobOpportunity::where('status', 'approved')
            ->where(function($q) {
                $q->whereNull('application_deadline')
                  ->orWhere('application_deadline', '>=', now()->toDateString());
            })
            ->orderBy('created_at', 'desc');

        // Global search across all fields (similar to ActivityLogController)
        if ($request->filled('global_search')) {
            $searchTerm = $request->global_search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('requirements', 'like', "%{$searchTerm}%")
                  ->orWhere('company', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%")
                  ->orWhere('job_type', 'like', "%{$searchTerm}%")
                  ->orWhere('salary_range', 'like', "%{$searchTerm}%");
            });
        }

        // Individual filters (similar to ActivityLogController)
        if ($request->filled('job_type_filter')) {
            $query->where('job_type', $request->job_type_filter);
        }

        if ($request->filled('location_filter')) {
            $query->where('location', 'like', "%{$request->location_filter}%");
        }

        if ($request->filled('company_filter')) {
            $query->where('company', 'like', "%{$request->company_filter}%");
        }

        // Date range filtering (similar to ActivityLogController)
        if ($request->filled('date_from')) {
            $dateFrom = \Carbon\Carbon::createFromFormat('Y-m-d', $request->date_from, 'Asia/Manila')
                ->startOfDay()
                ->utc();
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($request->filled('date_to')) {
            $dateTo = \Carbon\Carbon::createFromFormat('Y-m-d', $request->date_to, 'Asia/Manila')
                ->endOfDay()
                ->utc();
            $query->where('created_at', '<=', $dateTo);
        }

        $jobOpportunities = $query->get();

        // Get filter options for dropdowns
        $jobTypes = JobOpportunity::where('status', 'approved')
            ->where(function($q) {
                $q->whereNull('application_deadline')
                  ->orWhere('application_deadline', '>=', now()->toDateString());
            })
            ->select('job_type')
            ->distinct()
            ->orderBy('job_type')
            ->pluck('job_type')
            ->filter();

        $locations = JobOpportunity::where('status', 'approved')
            ->where(function($q) {
                $q->whereNull('application_deadline')
                  ->orWhere('application_deadline', '>=', now()->toDateString());
            })
            ->select('location')
            ->distinct()
            ->orderBy('location')
            ->pluck('location')
            ->filter();

        $companies = JobOpportunity::where('status', 'approved')
            ->where(function($q) {
                $q->whereNull('application_deadline')
                  ->orWhere('application_deadline', '>=', now()->toDateString());
            })
            ->select('company')
            ->distinct()
            ->orderBy('company')
            ->pluck('company')
            ->filter();

        // If it's an AJAX request, return JSON with rendered HTML
        if ($request->ajax()) {
            $html = view('user.partials.job-opportunities-list', compact('jobOpportunities'))->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $jobOpportunities->count()
            ]);
        }

        return view('user.job-opportunity', compact('jobOpportunities', 'jobTypes', 'locations', 'companies'));
    }

    public function show($id)
    {
        $jobOpportunity = JobOpportunity::findOrFail($id);
        
        // Only show approved and non-expired job opportunities
        if ($jobOpportunity->status !== 'approved' || $jobOpportunity->isExpired()) {
            abort(404, 'Job opportunity not found or has expired.');
        }
        
        return view('user.job-opportunity-detail', compact('jobOpportunity'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'job_type' => 'required|in:full-time,part-time,contract,internship,remote',
            'salary_range' => 'nullable|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'application_url' => 'nullable|url|max:255',
            'contact_email' => 'nullable|email|max:255',
            'application_deadline' => 'nullable|date|after:today',
            'attachments.*' => 'nullable|file|max:51200', // 50MB max per file
        ]);

        $user = Auth::user();
        
        // Handle attachments
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('job-opportunities', 'public');
                $attachmentPaths[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ];
            }
        }

        // If user is Staff or HR, create a pending change for approval
        if ($user->role === 'Staff' || $user->role === 'HR') {
            PendingChange::create([
                'staff_user_id' => $user->id,
                'change_type' => 'job_opportunity_creation',
                'change_data' => [
                    'title' => $request->title,
                    'company' => $request->company,
                    'location' => $request->location,
                    'job_type' => $request->job_type,
                    'salary_range' => $request->salary_range,
                    'description' => $request->description,
                    'requirements' => $request->requirements,
                    'application_url' => $request->application_url,
                    'contact_email' => $request->contact_email,
                    'contact_number' => $request->contact_number,
                    'application_deadline' => $request->application_deadline,
                    'attachments' => !empty($attachmentPaths) ? $attachmentPaths : null,
                ],
                'target_user_email' => $user->email,
                'status' => 'pending'
            ]);

            // Log the pending change submission
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'job_opportunity_pending',
                'description' => "Submitted job opportunity for approval: {$request->title} at {$request->company}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route('job-opportunity')->with('success', 
                'Your job opportunity has been submitted for admin approval.');
        }

        // Admin/SuperAdmin can create job opportunities directly
        $jobOpportunity = JobOpportunity::create([
            'title' => $request->title,
            'company' => $request->company,
            'location' => $request->location,
            'job_type' => $request->job_type,
            'salary_range' => $request->salary_range,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'application_url' => $request->application_url,
            'contact_email' => $request->contact_email,
            'contact_number' => $request->contact_number,
            'application_deadline' => $request->application_deadline,
            'user_id' => $user->id,
            'status' => 'approved',
            'attachments' => !empty($attachmentPaths) ? $attachmentPaths : null,
        ]);

        // Log the activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'job_opportunity_created',
            'description' => "Created job opportunity: {$jobOpportunity->title} at {$jobOpportunity->company}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('job-opportunity')->with('success', 'Job opportunity posted successfully!');
    }
}