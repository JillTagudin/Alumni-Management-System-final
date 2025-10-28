<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Alumni;
use App\Models\ActivityLog;

class MembershipController extends Controller
{
    /**
     * Display the membership page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is Admin, Staff, or SuperAdmin
        if (in_array($user->role, ['Admin', 'Staff', 'SuperAdmin'])) {
            // Get all alumni with membership data (no server-side filtering)
            $alumni = Alumni::orderBy('Fullname', 'asc')->get();
            
            // Get statistics for dashboard
            $stats = [
                'total_alumni' => Alumni::count(),
                'active_members' => Alumni::where('membership_status', 'Active')->count(),
                'pending_members' => Alumni::where('membership_status', 'Pending')->count(),
                'inactive_members' => Alumni::where('membership_status', 'Inactive')->count(),
                'regular_members' => Alumni::where('membership_type', 'Regular')->count(),
                'lifetime_members' => Alumni::where('membership_type', 'Lifetime')->count(),
            ];
            
            return view('membership.index', compact('user', 'alumni', 'stats'));
        }
        
        // For Alumni users, get their membership data
        $alumniRecord = Alumni::where('user_id', $user->id)->first();
        
        // Prepare membership data
        $membershipData = [
            'has_record' => $alumniRecord !== null,
            'status' => $alumniRecord->membership_status ?? 'Not Set',
            'type' => $alumniRecord->membership_type ?? 'Not Set',
            'payment_amount' => $alumniRecord->payment_amount ?? 0,
            'fullname' => $alumniRecord->Fullname ?? $user->fullname,
            'student_number' => $alumniRecord->student_number ?? 'Not Available',
            'course' => $alumniRecord->Course ?? 'Not Available',
            'batch' => $alumniRecord->Batch ?? 'Not Available',
            'created_at' => $alumniRecord->created_at ?? null,
            'updated_at' => $alumniRecord->updated_at ?? null,
        ];
        
        // Calculate membership status info
        $statusInfo = $this->getMembershipStatusInfo($membershipData['status']);
        
        return view('user.membership', compact('user', 'alumniRecord', 'membershipData', 'statusInfo'));
    }
    
    /**
     * Update membership status and type for an alumni
     */
    public function updateMembership(Request $request, $id)
    {
        $request->validate([
            'membership_status' => 'required|in:Active,Inactive,Pending',
            'membership_type' => 'required|in:Annual,Lifetime'
        ]);
        
        $alumni = Alumni::findOrFail($id);
        
        // Store old values for logging
        $oldStatus = $alumni->membership_status;
        $oldType = $alumni->membership_type;
        
        $alumni->update([
            'membership_status' => $request->membership_status,
            'membership_type' => $request->membership_type
        ]);
        
        // Log the membership update
        ActivityLog::log(
            'membership_updated',
            "Updated membership for {$alumni->Fullname} - Status: {$oldStatus} → {$request->membership_status}, Type: {$oldType} → {$request->membership_type}",
            Auth::id(),
            [
                'alumni_id' => $alumni->AlumniID,
                'student_number' => $alumni->student_number,
                'old_status' => $oldStatus,
                'new_status' => $request->membership_status,
                'old_type' => $oldType,
                'new_type' => $request->membership_type
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Membership updated successfully'
        ]);
    }
    
    /**
     * Get membership status information
     */
    private function getMembershipStatusInfo($status)
    {
        $statusInfo = [
            'Active' => [
                'color' => 'green',
                'icon' => 'check-circle',
                'message' => 'Your membership is active, thank you for joining us.',
                'action' => null
            ],
            'Pending' => [
                'color' => 'yellow',
                'icon' => 'clock',
                'message' => 'Your membership is pending approval. Please wait for confirmation.',
                'action' => 'Contact admin for status update'
            ],
            'Inactive' => [
                'color' => 'red',
                'icon' => 'x-circle',
                'message' => 'Your membership is inactive. You can renew to support our Alumni Management system.',
                'action' => 'Renew membership'
            ],
            'Not Set' => [
                'color' => 'gray',
                'icon' => 'question-mark-circle',
                'message' => 'No membership status found. Please contact admin to set up your membership.',
                'action' => 'Contact admin'
            ]
        ];
        
        return $statusInfo[$status] ?? $statusInfo['Not Set'];
    }
    
    /**
     * Get payment information based on membership type
     */
    private function getPaymentInfo($type)
    {
        $paymentInfo = [
            'Regular' => [
                'amount' => 500,
                'period' => 'Annual',
                'description' => 'Regular membership fee - paid annually'
            ],
            'Premium' => [
                'amount' => 1000,
                'period' => 'Annual',
                'description' => 'Premium membership fee - paid annually'
            ],
            'Lifetime' => [
                'amount' => 5000,
                'period' => 'One-time',
                'description' => 'Lifetime membership fee - one-time payment'
            ],
            'Not Set' => [
                'amount' => 0,
                'period' => 'N/A',
                'description' => 'Contact admin for pricing information'
            ]
        ];
        
        return $paymentInfo[$type] ?? $paymentInfo['Not Set'];
    }

    /**
     * API: Get all alumni payment records
     */
    public function apiPaymentRecords(Request $request)
    {
        $alumni = Alumni::select([
            'id', 'AlumniID', 'Fullname', 'membership_status', 
            'membership_type', 'payment_amount', 'created_at', 'updated_at'
        ])
        ->whereNotNull('payment_amount')
        ->where('payment_amount', '>', 0)
        ->orderBy('updated_at', 'desc')
        ->get();
        
        // Add payment info for each record
        $paymentRecords = $alumni->map(function ($alumnus) {
            $paymentInfo = $this->getPaymentInfo($alumnus->membership_type);
            return [
                'id' => $alumnus->id,
                'alumni_id' => $alumnus->AlumniID,
                'fullname' => $alumnus->Fullname,
                'membership_status' => $alumnus->membership_status,
                'membership_type' => $alumnus->membership_type,
                'payment_amount' => $alumnus->payment_amount,
                'expected_amount' => $paymentInfo['amount'],
                'payment_period' => $paymentInfo['period'],
                'payment_status' => $alumnus->payment_amount >= $paymentInfo['amount'] ? 'Paid' : 'Partial',
                'last_updated' => $alumnus->updated_at,
                'created_at' => $alumnus->created_at
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $paymentRecords,
            'total_records' => $paymentRecords->count(),
            'total_amount' => $alumni->sum('payment_amount')
        ]);
    }

    /**
     * API: Get payment statistics
     */
    public function apiPaymentStats(Request $request)
    {
        $stats = [
            'total_payments' => Alumni::whereNotNull('payment_amount')->where('payment_amount', '>', 0)->sum('payment_amount'),
            'total_paid_members' => Alumni::whereNotNull('payment_amount')->where('payment_amount', '>', 0)->count(),
            'pending_payments' => Alumni::where('membership_status', 'Pending')->count(),
            'active_paid_members' => Alumni::where('membership_status', 'Active')->whereNotNull('payment_amount')->where('payment_amount', '>', 0)->count(),
            'payment_breakdown' => [
                'regular' => Alumni::where('membership_type', 'Regular')->sum('payment_amount'),
                'premium' => Alumni::where('membership_type', 'Premium')->sum('payment_amount'),
                'lifetime' => Alumni::where('membership_type', 'Lifetime')->sum('payment_amount')
            ],
            'membership_distribution' => [
                'regular_count' => Alumni::where('membership_type', 'Regular')->count(),
                'premium_count' => Alumni::where('membership_type', 'Premium')->count(),
                'lifetime_count' => Alumni::where('membership_type', 'Lifetime')->count()
            ]
        ];
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'generated_at' => now()
        ]);
    }

    /**
     * API: Get specific alumni payment record
     */
    public function apiPaymentRecord(Request $request, $id)
    {
        $alumni = Alumni::findOrFail($id);
        $paymentInfo = $this->getPaymentInfo($alumni->membership_type);
        $statusInfo = $this->getMembershipStatusInfo($alumni->membership_status);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $alumni->id,
                'alumni_id' => $alumni->AlumniID,
                'fullname' => $alumni->Fullname,
                'membership_status' => $alumni->membership_status,
                'membership_type' => $alumni->membership_type,
                'payment_amount' => $alumni->payment_amount,
                'expected_amount' => $paymentInfo['amount'],
                'payment_period' => $paymentInfo['period'],
                'payment_description' => $paymentInfo['description'],
                'payment_status' => $alumni->payment_amount >= $paymentInfo['amount'] ? 'Paid' : 'Partial',
                'status_info' => $statusInfo,
                'last_updated' => $alumni->updated_at,
                'created_at' => $alumni->created_at
            ]
        ]);
    }

    /**
     * API: Get all alumni data (existing functionality enhanced)
     */
    public function apiIndex(Request $request)
    {
        $alumni = Alumni::with('user')->get();
        return response()->json([
            'success' => true,
            'data' => $alumni,
            'total' => $alumni->count()
        ]);
    }
    
    /**
     * Sync membership data with balance update records
     * This method fetches payment history and updates membership status automatically
     */
    public function syncWithBalanceUpdate()
    {
        try {
            // Fetch payment history from remote API with timeout and retry settings
            $response = Http::withToken(env('REMOTE_API_TOKEN'))
                ->acceptJson()
                ->timeout(30)
                ->retry(3, 1000)
                ->get(env('REMOTE_API_URL') . '/history');

            if (!$response->successful()) {
                \Log::error('Membership Sync API Error: ' . $response->status() . ' - ' . $response->body());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch balance update data. Status: ' . $response->status()
                ], 500);
            }

            $paymentHistory = $response->json();
            $updatedCount = 0;
            $updatedRecords = [];

            // Filter for Alumni Membership Fee transactions
            $membershipFeeTransactions = collect($paymentHistory)->filter(function ($transaction) {
                return isset($transaction['fee_type_name']) && 
                       $transaction['fee_type_name'] === 'Alumni Membership Fee';
            });

            foreach ($membershipFeeTransactions as $transaction) {
                // Find alumni record by student_number (which contains the Student ID)
                $alumni = Alumni::where('student_number', $transaction['student_id'])->first();
                
                if ($alumni) {
                    // Store old values for logging
                    $oldStatus = $alumni->membership_status;
                    $oldPaymentAmount = $alumni->payment_amount;
                    
                    // Update membership status to Active and set payment amount
                    $alumni->update([
                        'membership_status' => 'Active',
                        'payment_amount' => $transaction['amount']
                    ]);
                    
                    // Log the membership sync update
                    ActivityLog::log(
                        'membership_synced',
                        "Synced membership for {$alumni->Fullname} via balance update - Status: {$oldStatus} → Active, Payment: {$oldPaymentAmount} → {$transaction['amount']}",
                        Auth::id(),
                        [
                            'alumni_id' => $alumni->AlumniID,
                            'student_number' => $alumni->student_number,
                            'old_status' => $oldStatus,
                            'new_status' => 'Active',
                            'old_payment_amount' => $oldPaymentAmount,
                            'new_payment_amount' => $transaction['amount'],
                            'transaction_id' => $transaction['id'] ?? null,
                            'sync_source' => 'balance_update_api'
                        ]
                    );
                    
                    $updatedCount++;
                    $updatedRecords[] = [
                        'alumni_id' => $alumni->AlumniID,
                        'fullname' => $alumni->Fullname,
                        'payment_amount' => $transaction['amount'],
                        'transaction_date' => $transaction['created_at'] ?? null
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} membership records",
                'updated_count' => $updatedCount,
                'updated_records' => $updatedRecords
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error syncing data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get alumni chart data for dashboard
     */
    public function getAlumniChartData(Request $request)
    {
        try {
            $chartType = $request->get('type', 'all');
            
            $data = [];
            
            switch ($chartType) {
                case 'gender':
                    $rawData = Alumni::selectRaw('Gender, COUNT(*) as count')
                        ->whereNotNull('Gender')
                        ->groupBy('Gender')
                        ->get()
                        ->pluck('count', 'Gender');
                    $data = $this->formatChartData($rawData);
                    break;
                    
                case 'age':
                    $rawData = Alumni::selectRaw(
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
                    ->get()
                    ->pluck('count', 'age_group');
                    $data = $this->formatChartData($rawData);
                    break;
                    
                case 'employment':
                    $rawData = Alumni::selectRaw('Occupation, COUNT(*) as count')
                        ->whereNotNull('Occupation')
                        ->groupBy('Occupation')
                        ->orderBy('count', 'desc')
                        ->take(10)
                        ->get()
                        ->pluck('count', 'Occupation');
                    $data = $this->formatChartData($rawData);
                    break;
                    
                case 'course':
                    $rawData = Alumni::selectRaw('Course, COUNT(*) as count')
                        ->whereNotNull('Course')
                        ->groupBy('Course')
                        ->orderBy('count', 'desc')
                        ->get()
                        ->pluck('count', 'Course');
                    $data = $this->formatChartData($rawData);
                    break;
                    
                case 'membership':
                    $rawData = Alumni::selectRaw('membership_status, COUNT(*) as count')
                        ->whereNotNull('membership_status')
                        ->groupBy('membership_status')
                        ->get()
                        ->pluck('count', 'membership_status');
                    $data = $this->formatChartData($rawData);
                    break;
                    
                case 'location':
                    $rawData = Alumni::selectRaw('Address, COUNT(*) as count')
                        ->whereNotNull('Address')
                        ->groupBy('Address')
                        ->orderBy('count', 'desc')
                        ->take(10)
                        ->get()
                        ->pluck('count', 'Address');
                    $data = $this->formatChartData($rawData);
                    break;
                    
                case 'graduation':
                    $rawData = Alumni::selectRaw('Batch, COUNT(*) as count')
                        ->whereNotNull('Batch')
                        ->groupBy('Batch')
                        ->orderBy('Batch', 'desc')
                        ->get()
                        ->pluck('count', 'Batch');
                    $data = $this->formatChartData($rawData);
                    break;
                    
                case 'trends':
                    $rawData = Alumni::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                        ->groupBy('month')
                        ->orderBy('month')
                        ->get()
                        ->pluck('count', 'month');
                    $data = $this->formatChartData($rawData);
                    break;
                    
                case 'all':
                default:
                    // Return all chart types
                    $data = [
                        'gender' => $this->formatChartData(Alumni::selectRaw('Gender, COUNT(*) as count')->whereNotNull('Gender')->groupBy('Gender')->pluck('count', 'Gender')),
                        'age' => $this->formatChartData(Alumni::selectRaw('CASE WHEN Age < 25 THEN "18-24" WHEN Age < 35 THEN "25-34" WHEN Age < 45 THEN "35-44" WHEN Age < 55 THEN "45-54" ELSE "55+" END as age_group, COUNT(*) as count')->whereNotNull('Age')->groupBy('age_group')->pluck('count', 'age_group')),
                        'employment' => $this->formatChartData(Alumni::selectRaw('Occupation, COUNT(*) as count')->whereNotNull('Occupation')->groupBy('Occupation')->orderBy('count', 'desc')->take(10)->pluck('count', 'Occupation')),
                        'course' => $this->formatChartData(Alumni::selectRaw('Course, COUNT(*) as count')->whereNotNull('Course')->groupBy('Course')->orderBy('count', 'desc')->pluck('count', 'Course')),
                        'membership' => $this->formatChartData(Alumni::selectRaw('membership_status, COUNT(*) as count')->whereNotNull('membership_status')->groupBy('membership_status')->pluck('count', 'membership_status')),
                        'location' => $this->formatChartData(Alumni::selectRaw('Address, COUNT(*) as count')->whereNotNull('Address')->groupBy('Address')->orderBy('count', 'desc')->take(10)->pluck('count', 'Address')),
                        'graduation' => $this->formatChartData(Alumni::selectRaw('Batch, COUNT(*) as count')->whereNotNull('Batch')->groupBy('Batch')->orderBy('Batch', 'desc')->pluck('count', 'Batch')),
                        'trends' => $this->formatChartData(Alumni::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')->groupBy('month')->orderBy('month')->pluck('count', 'month'))
                    ];
                    break;
            }
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching chart data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Format data for Chart.js (pie/bar charts)
     */
    private function formatChartData($rawData)
    {
        return [
            'labels' => array_keys($rawData->toArray()),
            'data' => array_values($rawData->toArray())
        ];
    }

    /**
     * API: Get common chatbot queries
     */
    public function getCommonQueries(Request $request)
    {
        try {
            $commonQueries = [
                [
                    'id' => 1,
                    'question' => 'How do I update my membership status?',
                    'answer' => 'You can update your membership status by contacting the admin or through the membership portal.',
                    'category' => 'membership'
                ],
                [
                    'id' => 2,
                    'question' => 'What are the membership fees?',
                    'answer' => 'Regular membership is ₱500 annually, Premium is ₱1000 annually, and Lifetime is ₱5000 one-time.',
                    'category' => 'payment'
                ],
                [
                    'id' => 3,
                    'question' => 'How can I access alumni benefits?',
                    'answer' => 'Alumni benefits are available to active members. Please ensure your membership status is active.',
                    'category' => 'benefits'
                ],
                [
                    'id' => 4,
                    'question' => 'How do I contact other alumni?',
                    'answer' => 'You can use the alumni directory feature available to active members.',
                    'category' => 'networking'
                ],
                [
                    'id' => 5,
                    'question' => 'What events are available for alumni?',
                    'answer' => 'Check the events section for upcoming alumni gatherings, workshops, and networking events.',
                    'category' => 'events'
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $commonQueries
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching common queries: ' . $e->getMessage()
            ], 500);
        }
    }
}




            








