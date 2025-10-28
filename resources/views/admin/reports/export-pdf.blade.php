<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ucfirst($type) }} Export Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            background-color: white;
        }
        .header {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            background-color: white;
            position: relative;
            height: 120px;
        }
        .header-logo-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 80px;
            height: auto;
        }
        .header-logo-right {
            position: absolute;
            right: 0;
            top: 0;
            width: 80px;
            height: auto;
        }
        .header-content {
            text-align: center;
            margin: 0 100px;
            padding-top: 10px;
        }
        .header-content h1 {
            margin: 0;
            color: #333;
            font-size: 18px;
            font-weight: bold;
        }
        .header-content .college-name {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
            font-weight: bold;
        }
        .header-content .college-subtitle {
            margin: 2px 0;
            color: #666;
            font-size: 12px;
            font-style: italic;
        }
        .header-content .address {
            margin: 2px 0;
            color: #666;
            font-size: 10px;
        }
        .header-content .report-info {
            margin: 8px 0 0 0;
            color: #333;
            font-size: 11px;
        }
        .summary-section {
            margin-bottom: 30px;
        }
        .summary-section h3 {
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .summary-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .summary-card h4 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .summary-card .number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-resolved { background-color: #d4edda; color: #155724; }
        .status-in_progress { background-color: #cce7ff; color: #004085; }
        .priority-high { background-color: #f8d7da; color: #721c24; }
        .priority-medium { background-color: #fff3cd; color: #856404; }
        .priority-low { background-color: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('logo/bcp.png') }}" alt="BCP Logo" class="header-logo-left">
        <div class="header-content">
            <div class="college-name">BESTLINK COLLEGE OF THE PHILIPPINES</div>
            <div class="college-subtitle">College of Computer Studies</div>
            <div class="address">1071 Rizal Extension, Quirino Highway, Novaliches, Quezon City</div>
            <h1>{{ ucfirst($type) }} Export Report</h1>
            <div class="report-info">
                <div>Generated on: {{ $generatedAt->format('F d, Y \\a\\t g:i A') }}</div>
            </div>
        </div>
        <img src="{{ public_path('logo/bcp.png') }}" alt="BCP Logo" class="header-logo-right">
    </div>

    @if($type === 'summary')
        <div class="summary-section">
            <h3>System Summary</h3>
            <div class="summary-grid">
                <div class="summary-card">
                    <h4>Total Users</h4>
                    <div class="number">{{ $data['total_users'] ?? 0 }}</div>
                </div>
                <div class="summary-card">
                    <h4>Total Alumni</h4>
                    <div class="number">{{ $data['total_alumni'] ?? 0 }}</div>
                </div>
                <div class="summary-card">
                    <h4>Active Alumni</h4>
                    <div class="number">{{ $data['active_alumni'] ?? 0 }}</div>
                </div>
                <div class="summary-card">
                    <h4>Recent Activities</h4>
                    <div class="number">{{ $data['recent_activities'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Users Section -->
        @if(isset($data['users']) && $data['users']->count() > 0)
            <div class="summary-section">
                <h3>Recent Users ({{ $data['users']->count() }})</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['users'] as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ ucfirst($user->role) }}</td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Alumni Section -->
        @if(isset($data['alumni']) && $data['alumni']->count() > 0)
            <div class="summary-section">
                <h3>Recent Alumni ({{ $data['alumni']->count() }})</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Alumni ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Batch</th>
                            <th>Registered Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['alumni'] as $alumni)
                            <tr>
                                <td>{{ $alumni->id }}</td>
                                <td>{{ $alumni->alumni_id }}</td>
                                <td>{{ $alumni->first_name }} {{ $alumni->last_name }}</td>
                                <td>{{ $alumni->email }}</td>
                                <td>{{ $alumni->course }}</td>
                                <td>{{ $alumni->batch }}</td>
                                <td>{{ $alumni->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Feedback Section -->
        @if(isset($data['feedback']) && $data['feedback']->count() > 0)
            <div class="summary-section">
                <h3>Recent Feedback ({{ $data['feedback']->count() }})</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Subject</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['feedback'] as $feedback)
                            <tr>
                                <td>{{ $feedback->id }}</td>
                                <td>{{ $feedback->user->name }}</td>
                                <td>{{ $feedback->subject }}</td>
                                <td>{{ ucfirst($feedback->category) }}</td>
                                <td><span class="status-badge priority-{{ $feedback->priority }}">{{ ucfirst($feedback->priority) }}</span></td>
                                <td><span class="status-badge status-{{ $feedback->status }}">{{ ucfirst(str_replace('_', ' ', $feedback->status)) }}</span></td>
                                <td>{{ $feedback->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Activities Section -->
        @if(isset($data['activities']) && $data['activities']->count() > 0)
            <div class="summary-section">
                <h3>Recent Activities ({{ $data['activities']->count() }})</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['activities'] as $activity)
                            <tr>
                                <td>{{ $activity->id }}</td>
                                <td>{{ $activity->user ? $activity->user->name : 'System' }}</td>
                                <td>{{ $activity->action }}</td>
                                <td>{{ $activity->description }}</td>
                                <td>{{ $activity->created_at->format('M d, Y g:i A') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif

    <div class="footer">
        <p>This report was automatically generated by the Alumni Management System.</p>
        <p>© {{ date('Y') }} Bestlink College of the Philippines. All rights reserved.</p>
    </div>
</body>
</html>