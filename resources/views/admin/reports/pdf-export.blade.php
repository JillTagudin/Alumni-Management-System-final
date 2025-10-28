<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $data['title'] ?? 'Alumni Report' }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            background-color: white;
            color: #333;
            line-height: 1.3;
        }
        .header {
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            text-align: center;
            position: relative;
        }
        .header-logo-left {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 80px;
            height: 80px;
        }
        .header-logo-right {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 80px;
            height: 80px;
        }
        .header-content {
            margin: 0 100px;
            padding-top: 10px;
        }
        .header h1 {
            margin: 5px 0;
            color: #333;
            font-size: 16px;
            font-weight: bold;
        }
        .header .college-name {
            margin: 2px 0;
            color: #666;
            font-size: 13px;
            font-weight: bold;
        }
        .header .college-subtitle {
            margin: 1px 0;
            color: #666;
            font-size: 11px;
            font-style: italic;
        }
        .header .address {
            margin: 1px 0;
            color: #666;
            font-size: 9px;
        }
        .header .report-info {
            margin: 5px 0 0 0;
            color: #333;
            font-size: 10px;
        }
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            padding: 10px;
            margin-bottom: 15px;
        }
        .summary-box h3 {
            margin: 0 0 8px 0;
            color: #333;
            font-size: 13px;
        }
        .summary-box p {
            margin: 3px 0;
            font-size: 10px;
            line-height: 1.3;
        }
        .charts-section {
            margin: 15px 0;
            page-break-inside: avoid;
        }
        .charts-section h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }
        .chart-container {
            margin: 15px 0;
            text-align: center;
            page-break-inside: avoid;
        }
        .chart-container h4 {
            margin: 0 0 8px 0;
            color: #333;
            font-size: 12px;
            font-weight: bold;
        }
        .chart-image {
            max-width: 100%;
            height: auto;
            max-height: 300px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .ai-insights {
            background-color: #e8f4fd;
            border: 1px solid #b8daff;
            border-radius: 3px;
            padding: 10px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .ai-insights h3 {
            margin: 0 0 8px 0;
            color: #0056b3;
            font-size: 13px;
            font-weight: bold;
        }
        .ai-insights .section {
            margin-bottom: 10px;
        }
        .ai-insights .section h4 {
            margin: 0 0 5px 0;
            color: #0056b3;
            font-size: 11px;
            font-weight: bold;
        }
        .ai-insights .section p {
            margin: 3px 0;
            font-size: 10px;
            line-height: 1.3;
        }
        .ai-insights .section ul {
            margin: 3px 0;
            padding-left: 12px;
        }
        .ai-insights .section li {
            margin: 2px 0;
            font-size: 10px;
            line-height: 1.3;
        }
        .data-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .data-section h3 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 13px;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
            font-size: 10px;
        }
        .data-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }
        .ai-badge {
            background-color: #0056b3;
            color: white;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
        }
        /* Optimize space usage */
        .content-grid {
            display: block;
        }
        .two-column {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            margin-right: 2%;
        }
        .two-column:last-child {
            margin-right: 0;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <!-- Left Logo -->
        <img src="{{ public_path('logo/bcp.png') }}" alt="BCP Logo" class="header-logo-left">
        
        <!-- Right Logo -->
        <img src="{{ public_path('logo/bcp.png') }}" alt="Philippines Logo" class="header-logo-right">
        
        <!-- Header Content -->
        <div class="header-content">
            <div class="college-name">BESTLINK COLLEGE OF THE PHILIPPINES</div>
            <div class="college-subtitle">College of Computer Studies</div>
            <div class="address">1071 Rizal Extension, Quirino Highway, Novaliches, Quezon City</div>
            <h1>{{ $data['title'] ?? 'Alumni Report' }}</h1>
            <div class="report-info">
                Generated on: {{ date('F j, Y \a\t g:i A') }}
            </div>
        </div>
    </div>

    <!-- Executive Summary -->
    <div class="summary-box">
        <h3>Executive Summary</h3>
        <div class="content-grid">
            <div class="two-column">
                <p><strong>Total Alumni:</strong> {{ $data['total_alumni'] ?? 0 }}</p>
                <p><strong>Report Type:</strong> {{ ucfirst($type ?? 'Summary') }} Report</p>
            </div>
            <div class="two-column">
                <p><strong>Generated:</strong> {{ date('F j, Y \a\t g:i A') }}</p>
                <p><strong>Status:</strong> Complete with Visual Analytics</p>
            </div>
        </div>
    </div>

    <!-- Comprehensive Report Overview -->
    <div class="summary-box" style="margin-bottom: 20px;">
        <h3>Report Overview & Key Statistics</h3>
        
        <!-- Alumni Demographics Summary -->
        <div style="margin-bottom: 15px;">
            <h4 style="margin: 0 0 8px 0; color: #333; font-size: 12px; font-weight: bold;">Alumni Demographics</h4>
            <div class="content-grid">
                <div class="two-column">
                    @php
                        $totalAlumni = \App\Models\Alumni::count();
                        $maleCount = \App\Models\Alumni::where('Gender', 'Male')->count();
                        $femaleCount = \App\Models\Alumni::where('Gender', 'Female')->count();
                        $malePercentage = $totalAlumni > 0 ? round(($maleCount / $totalAlumni) * 100, 1) : 0;
                        $femalePercentage = $totalAlumni > 0 ? round(($femaleCount / $totalAlumni) * 100, 1) : 0;
                    @endphp
                    <p><strong>Gender Distribution:</strong></p>
                    <p style="margin-left: 10px;">• Male: {{ $maleCount }} ({{ $malePercentage }}%)</p>
                    <p style="margin-left: 10px;">• Female: {{ $femaleCount }} ({{ $femalePercentage }}%)</p>
                </div>
                <div class="two-column">
                    @php
                        $youngAdults = \App\Models\Alumni::whereBetween('Age', [18, 25])->count();
                        $adults = \App\Models\Alumni::whereBetween('Age', [26, 35])->count();
                        $middleAged = \App\Models\Alumni::where('Age', '>', 35)->count();
                    @endphp
                    <p><strong>Age Distribution:</strong></p>
                    <p style="margin-left: 10px;">• 18-25 years: {{ $youngAdults }}</p>
                    <p style="margin-left: 10px;">• 26-35 years: {{ $adults }}</p>
                    <p style="margin-left: 10px;">• 36+ years: {{ $middleAged }}</p>
                </div>
            </div>
        </div>

        <!-- Membership & Employment Summary -->
        <div style="margin-bottom: 15px;">
            <h4 style="margin: 0 0 8px 0; color: #333; font-size: 12px; font-weight: bold;">Membership & Employment Status</h4>
            <div class="content-grid">
                <div class="two-column">
                    @php
                        $activeMembers = \App\Models\Alumni::where('membership_status', 'Active')->count();
                        $pendingMembers = \App\Models\Alumni::where('membership_status', 'Pending')->count();
                        $inactiveMembers = \App\Models\Alumni::where('membership_status', 'Inactive')->count();
                    @endphp
                    <p><strong>Membership Status:</strong></p>
                    <p style="margin-left: 10px;">• Active: {{ $activeMembers }}</p>
                    <p style="margin-left: 10px;">• Pending: {{ $pendingMembers }}</p>
                    <p style="margin-left: 10px;">• Inactive: {{ $inactiveMembers }}</p>
                </div>
                <div class="two-column">
                    @php
                        // Fix employment rate calculation - exclude "Not Specified" and similar values
                        $employed = \App\Models\Alumni::whereNotNull('Occupation')
                            ->where('Occupation', '!=', '')
                            ->where('Occupation', '!=', 'Not Specified')
                            ->where('Occupation', '!=', '(Not Specified)')
                            ->where('Occupation', '!=', 'N/A')
                            ->where('Occupation', '!=', 'None')
                            ->count();
                        $unemployed = $totalAlumni - $employed;
                        $employmentRate = $totalAlumni > 0 ? round(($employed / $totalAlumni) * 100, 1) : 0;
                    @endphp
                    <p><strong>Employment Status:</strong></p>
                    <p style="margin-left: 10px;">• Employed: {{ $employed }} ({{ $employmentRate }}%)</p>
                    <p style="margin-left: 10px;">• Unemployed/Unknown: {{ $unemployed }}</p>
                </div>
            </div>
        </div>

        <!-- Academic Programs Summary -->
        <div style="margin-bottom: 15px;">
            <h4 style="margin: 0 0 8px 0; color: #333; font-size: 12px; font-weight: bold;">Academic Programs & Graduation Trends</h4>
            <div class="content-grid">
                <div class="two-column">
                    @php
                        $topCourses = \App\Models\Alumni::select('Course', \DB::raw('count(*) as count'))
                            ->whereNotNull('Course')
                            ->groupBy('Course')
                            ->orderBy('count', 'desc')
                            ->limit(3)
                            ->get();
                    @endphp
                    <p><strong>Top Programs:</strong></p>
                    @foreach($topCourses as $course)
                        <p style="margin-left: 10px;">• {{ $course->Course }}: {{ $course->count }}</p>
                    @endforeach
                </div>
                <div class="two-column">
                    @php
                        $recentBatches = \App\Models\Alumni::select('Batch', \DB::raw('count(*) as count'))
                            ->whereNotNull('Batch')
                            ->groupBy('Batch')
                            ->orderBy('Batch', 'desc')
                            ->limit(3)
                            ->get();
                    @endphp
                    <p><strong>Recent Batches:</strong></p>
                    @foreach($recentBatches as $batch)
                        <p style="margin-left: 10px;">• Batch {{ $batch->Batch }}: {{ $batch->count }} graduates</p>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Report Contents Preview -->
        <div style="border-top: 1px solid #ddd; padding-top: 10px;">
            <h4 style="margin: 0 0 8px 0; color: #333; font-size: 12px; font-weight: bold;">This Report Contains:</h4>
            <div class="content-grid">
                <div class="two-column">
                    <p style="font-size: 10px;">✓ Visual Charts & Analytics</p>
                    <p style="font-size: 10px;">✓ AI-Powered Insights & Analysis</p>
                    <p style="font-size: 10px;">✓ Demographic Breakdowns</p>
                </div>
                <div class="two-column">
                    <p style="font-size: 10px;">✓ Employment Status Reports</p>
                    <p style="font-size: 10px;">✓ Membership Analytics</p>
                    <p style="font-size: 10px;">✓ Strategic Recommendations</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Statistics Cards -->
    <div style="display: block; margin-bottom: 20px;">
        <h3 style="margin: 0 0 10px 0; color: #333; font-size: 14px; text-align: center;">Key Performance Indicators</h3>
        <div class="content-grid">
            <div class="two-column">
                <div style="background: #e8f5e8; border: 1px solid #4caf50; border-radius: 3px; padding: 8px; text-align: center; margin-bottom: 8px;">
                    @php
                        // Recalculate employment rate for KPI section with proper exclusions
                        $kpiEmployed = \App\Models\Alumni::whereNotNull('Occupation')
                            ->where('Occupation', '!=', '')
                            ->where('Occupation', '!=', 'Not Specified')
                            ->where('Occupation', '!=', '(Not Specified)')
                            ->where('Occupation', '!=', 'N/A')
                            ->where('Occupation', '!=', 'None')
                            ->count();
                        $kpiEmploymentRate = $totalAlumni > 0 ? round(($kpiEmployed / $totalAlumni) * 100, 1) : 0;
                    @endphp
                    <p style="margin: 0; font-size: 18px; font-weight: bold; color: #2e7d32;">{{ $kpiEmploymentRate }}%</p>
                    <p style="margin: 0; font-size: 10px; color: #2e7d32;">Employment Rate</p>
                </div>
                <div style="background: #fff3e0; border: 1px solid #ff9800; border-radius: 3px; padding: 8px; text-align: center;">
                    <p style="margin: 0; font-size: 18px; font-weight: bold; color: #f57c00;">{{ $activeMembers }}</p>
                    <p style="margin: 0; font-size: 10px; color: #f57c00;">Active Members</p>
                </div>
            </div>
            <div class="two-column">
                <div style="background: #e3f2fd; border: 1px solid #2196f3; border-radius: 3px; padding: 8px; text-align: center; margin-bottom: 8px;">
                    <p style="margin: 0; font-size: 18px; font-weight: bold; color: #1976d2;">{{ $totalAlumni }}</p>
                    <p style="margin: 0; font-size: 10px; color: #1976d2;">Total Alumni</p>
                </div>
                <div style="background: #fce4ec; border: 1px solid #e91e63; border-radius: 3px; padding: 8px; text-align: center;">
                    @php
                        $currentYear = date('Y');
                        // Fix recent graduates calculation - include current year and previous year
                        $recentGraduates = \App\Models\Alumni::where('Batch', '>=', $currentYear - 1)->count();
                    @endphp
                    <p style="margin: 0; font-size: 18px; font-weight: bold; color: #c2185b;">{{ $recentGraduates }}</p>
                    <p style="margin: 0; font-size: 10px; color: #c2185b;">Recent Graduates ({{ $currentYear - 1 }}-{{ $currentYear }})</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    @if(isset($chartImages) && !empty($chartImages))
    <div class="charts-section">
        <h3>Alumni Records & Membership Analytics - Visual Charts</h3>
        
        @if(isset($chartImages['gender']) && $chartImages['gender'])
        <div class="chart-container">
            <h4>Gender Distribution</h4>
            <img src="{{ $chartImages['gender'] }}" alt="Gender Distribution Chart" class="chart-image">
        </div>
        @endif

        @if(isset($chartImages['age']) && $chartImages['age'])
        <div class="chart-container">
            <h4>Age Groups Distribution</h4>
            <img src="{{ $chartImages['age'] }}" alt="Age Groups Chart" class="chart-image">
        </div>
        @endif

        @if(isset($chartImages['membership']) && $chartImages['membership'])
        <div class="chart-container">
            <h4>Membership Status</h4>
            <img src="{{ $chartImages['membership'] }}" alt="Membership Status Chart" class="chart-image">
        </div>
        @endif

        @if(isset($chartImages['employment']) && $chartImages['employment'])
        <div class="chart-container">
            <h4>Employment Status</h4>
            <img src="{{ $chartImages['employment'] }}" alt="Employment Status Chart" class="chart-image">
        </div>
        @endif

        @if(isset($chartImages['courses']) && $chartImages['courses'])
        <div class="chart-container">
            <h4>Alumni by Course</h4>
            <img src="{{ $chartImages['courses'] }}" alt="Alumni by Course Chart" class="chart-image">
        </div>
        @endif

        @if(isset($chartImages['trends']) && $chartImages['trends'])
        <div class="chart-container">
            <h4>Registration Trends</h4>
            <img src="{{ $chartImages['trends'] }}" alt="Registration Trends Chart" class="chart-image">
        </div>
        @endif

        @if(isset($chartImages['batch']) && $chartImages['batch'])
        <div class="chart-container">
            <h4>Alumni by Batch</h4>
            <img src="{{ $chartImages['batch'] }}" alt="Alumni by Batch Chart" class="chart-image">
        </div>
        @endif
    </div>
    @endif

    <!-- AI-Generated Insights -->
    @if(isset($aiInsights))
    <div class="ai-insights">
        <h3><span class="ai-badge">AI POWERED</span> Comprehensive Analysis & Insights</h3>
        
        <div class="section">
            <h4>Executive Summary</h4>
            <p>{{ $aiInsights['summary'] }}</p>
        </div>

        @if(!empty($aiInsights['key_findings']))
        <div class="section">
            <h4>Key Findings</h4>
            <ul>
                @foreach($aiInsights['key_findings'] as $finding)
                <li>{{ $finding }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(!empty($aiInsights['trends']))
        <div class="section">
            <h4>Observed Trends</h4>
            <ul>
                @foreach($aiInsights['trends'] as $trend)
                <li>{{ $trend }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(!empty($aiInsights['recommendations']))
        <div class="section">
            <h4>Strategic Recommendations</h4>
            <ul>
                @foreach($aiInsights['recommendations'] as $recommendation)
                <li>{{ $recommendation }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(!empty($aiInsights['predictions']))
        <div class="section">
            <h4>Future Predictions</h4>
            <ul>
                @foreach($aiInsights['predictions'] as $prediction)
                <li>{{ $prediction }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <p style="font-size: 9px; color: #666; margin-top: 8px; font-style: italic;">
            AI Analysis generated on {{ $aiInsights['generated_at'] ?? date('F j, Y \a\t g:i A') }}
        </p>
    </div>
    @endif

    <!-- Data Sections -->
    @if(isset($data['sections']) && is_array($data['sections']))
        @foreach($data['sections'] as $section)
        <div class="data-section">
            <h3>{{ $section['title'] ?? 'Data Section' }}</h3>
            
            @if(isset($section['data']) && is_array($section['data']) && count($section['data']) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Count</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($section['data'] as $item)
                    <tr>
                        <td>{{ $item['category'] ?? 'N/A' }}</td>
                        <td>{{ $item['count'] ?? 0 }}</td>
                        <td>{{ $item['percentage'] ?? '0.00' }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="font-style: italic; color: #666;">No data available for this section.</p>
            @endif
        </div>
        @endforeach
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This report was generated by the Alumni Management System</p>
        <p>© {{ date('Y') }} Bestlink College of the Philippines - All Rights Reserved</p>
        @if(isset($aiInsights))
        <p><span class="ai-badge">AI ENHANCED</span> Report includes AI-powered analysis and insights</p>
        @endif
        @if(isset($chartImages) && !empty($chartImages))
        <p><strong>VISUAL ANALYTICS</strong> Report includes interactive charts and graphs</p>
        @endif
    </div>
</body>
</html>