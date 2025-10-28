<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\ActivityLog;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class StudentApiController extends Controller
{
  protected $client;

      public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('API_URL'),
        ]);
    }

    public function fetchStudents()
    {
        try {
            $response = $this->client->get('students', [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . env('API_TOKEN'),
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            return collect($result['data'] ?? $result);
        } catch (\Exception $e) {
            return collect();
        }
    }
    
    public function sectionsFromApi()
    {
        try {
            $response = $this->client->get('students', [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . env('API_TOKEN'),
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            $students = $result['data'] ?? $result;


            $sections = collect($students)
                ->pluck('section')   
                ->filter()
                ->unique()
                ->values();

            return $sections;

        } catch (\Exception $e) {
            return collect(); 
            
        }
    }

    public function index()
    {
        return $this->fetchFromApi('students', 'students.index');
    }

    public function college()
    {
        return $this->fetchFromApi('students/college', 'students.college');
    }

    public function seniorHigh()
    {
        return $this->fetchFromApi('students/senior-high', 'students.senior-high');
    }

    /**
     * Sync students with alumni records to identify unregistered students
     * This method compares student records from external API with alumni database
     */
    public function syncWithAlumniRecords()
    {
        try {
            // Fetch students from external API
            $response = $this->client->get('students', [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . env('API_TOKEN'),
                ],
            ]);

            // Check response status code - accept both 200 and 201 as successful
            $statusCode = $response->getStatusCode();
            if (!in_array($statusCode, [200, 201])) {
                \Log::error('Student Sync API Error: ' . $statusCode . ' - ' . $response->getBody());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch student data. Status: ' . $statusCode
                ], 500);
            }

            $studentsData = json_decode($response->getBody()->getContents(), true);
            $students = collect($studentsData['data'] ?? $studentsData);

            $unregisteredStudents = [];
            $registeredCount = 0;
            $totalStudents = $students->count();

            foreach ($students as $student) {
                // Check if student exists in alumni records by student_number (primary identifier)
                $existsInAlumni = Alumni::where('student_number', $student['student_number'] ?? '')->exists();

                if (!$existsInAlumni) {
                    $unregisteredStudents[] = [
                        'student_number' => $student['student_number'] ?? 'N/A',
                        'name' => $student['name'] ?? 'N/A',
                        'email' => $student['email'] ?? 'N/A',
                        'program' => $student['program'] ?? 'N/A',
                        'section' => $student['section'] ?? 'N/A',
                        'year_level' => $student['year_level'] ?? 'N/A'
                    ];
                } else {
                    $registeredCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Sync completed successfully",
                'total_students' => $totalStudents,
                'registered_count' => $registeredCount,
                'unregistered_count' => count($unregisteredStudents),
                'unregistered_students' => $unregisteredStudents
            ]);

        } catch (\Exception $e) {
            \Log::error('Student-Alumni Sync Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing data: ' . $e->getMessage()
            ], 500);
        }
    }

    private function fetchFromApi($endpoint, $view)
    {
        try {
            $response = $this->client->get($endpoint, [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . env('API_TOKEN'),
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

         
            $students = $result['data'] ?? $result;

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch students: ' . $e->getMessage());
        }

        return view($view, compact('students'));
    }

    public function sections()
    {
        try {
            $response = $this->client->get('sections', [ 
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . env('API_TOKEN'),
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            $sections = $result['data'] ?? $result;

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to fetch sections: ' . $e->getMessage());
        }

        return $sections;
    }

    public function sendRegistrationInvitations(Request $request)
    {
        $request->validate([
            'type' => 'required|in:unregistered,selected'
        ]);

        try {
            // Get all students from API
            $students = $this->fetchStudents();
            
            if ($students->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No students found from API'
                ]);
            }

            // Get registered alumni student numbers
            $registeredStudentNumbers = Alumni::pluck('student_number')->toArray();
            
            // Filter unregistered students
            $unregisteredStudents = $students->filter(function ($student) use ($registeredStudentNumbers) {
                return !in_array($student['student_number'], $registeredStudentNumbers);
            });

            if ($unregisteredStudents->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'All students are already registered in the alumni system!',
                    'details' => [
                        'total_unregistered' => 0,
                        'success_count' => 0,
                        'failed_count' => 0
                    ]
                ]);
            }

            $successCount = 0;
            $failedCount = 0;
            $registrationLink = 'https://alumni.bestlink-sms.com/login';

            foreach ($unregisteredStudents as $student) {
                try {
                    // Skip if no email
                    if (empty($student['email'])) {
                        $failedCount++;
                        continue;
                    }

                    Mail::send('emails.alumni-registration-invitation', [
                        'studentName' => $student['name'],
                        'studentNumber' => $student['student_number'],
                        'program' => $student['program'],
                        'email' => $student['email'],
                        'registrationLink' => $registrationLink,
                        'sentAt' => now()->format('F j, Y \\a\\t g:i A')
                    ], function ($message) use ($student) {
                        $message->to($student['email'], $student['name'])
                               ->subject('🎓 Join Our Alumni Network - Stay Connected!');
                    });
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $failedCount++;
                    continue;
                }
            }

            // Log the activity
            ActivityLog::log(
                'alumni_invitation_sent',
                "Alumni registration invitations sent to {$successCount} unregistered students",
                Auth::id(),
                [
                    'total_unregistered' => $unregisteredStudents->count(),
                    'success_count' => $successCount,
                    'failed_count' => $failedCount,
                    'registration_link' => $registrationLink
                ]
            );

            return response()->json([
                'success' => true,
                'message' => "Registration invitations sent successfully to {$successCount} students!",
                'details' => [
                    'total_unregistered' => $unregisteredStudents->count(),
                    'success_count' => $successCount,
                    'failed_count' => $failedCount
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send invitations: ' . $e->getMessage()
            ]);
        }
    }
}