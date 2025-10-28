<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ ucfirst($reportType) }} Report</title>
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
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h3 {
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
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
            <h1>{{ ucfirst($reportType) }} Report</h1>
            <div class="report-info">
                <div>Generated on: {{ $generatedAt->format('F d, Y \\a\\t g:i A') }}</div>
                @if($dateFrom && $dateTo)
                    <div>Period: {{ $dateFrom->format('M d, Y') }} - {{ $dateTo->format('M d, Y') }}</div>
                @endif
            </div>
        </div>
        <img src="{{ public_path('logo/bcp.png') }}" alt="BCP Logo" class="header-logo-right">
    </div>

    @if($reportType === 'users')
        <div class="info-section">
            <h3>User Report ({{ $data->count() }} users)</h3>
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
                    @foreach($data as $user)
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

    @if($reportType === 'alumni')
        <div class="info-section">
            <h3>Alumni Report ({{ $data->count() }} alumni)</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Alumni ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Batch</th>
                        <th>Membership Status</th>
                        <th>Registered Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $alumni)
                        <tr>
                            <td>{{ $alumni->id }}</td>
                            <td>{{ $alumni->alumni_id }}</td>
                            <td>{{ $alumni->first_name }} {{ $alumni->last_name }}</td>
                            <td>{{ $alumni->email }}</td>
                            <td>{{ $alumni->course }}</td>
                            <td>{{ $alumni->batch }}</td>
                            <td>{{ ucfirst($alumni->membership_status ?? 'N/A') }}</td>
                            <td>{{ $alumni->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if($reportType === 'feedback')
        <div class="info-section">
            <h3>Feedback Report ({{ $data->count() }} feedback entries)</h3>
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
                    @foreach($data as $feedback)
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

    @if($reportType === 'activities')
        <div class="info-section">
            <h3>Activity Report ({{ $data->count() }} activities)</h3>
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
                    @foreach($data as $activity)
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

    @if($reportType === 'pending_changes')
        <div class="info-section">
            <h3>Pending Changes Report ({{ $data->count() }} pending changes)</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Field</th>
                        <th>Old Value</th>
                        <th>New Value</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $change)
                        <tr>
                            <td>{{ $change->id }}</td>
                            <td>{{ $change->user->name }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $change->field_name)) }}</td>
                            <td>{{ $change->old_value }}</td>
                            <td>{{ $change->new_value }}</td>
                            <td>{{ ucfirst($change->status) }}</td>
                            <td>{{ $change->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>This report was automatically generated by the Alumni Management System.</p>
        <p>© {{ date('Y') }} Bestlink College of the Philippines. All rights reserved.</p>
    </div>
</body>
</html>