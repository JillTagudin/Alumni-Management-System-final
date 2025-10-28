<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Real-time indicator -->
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center">
                    <div id="status-indicator" class="w-3 h-3 bg-green-500 rounded-full flex items-center justify-center"></div>
                    <span class="text-sm text-gray-600">Live Updates Active</span>
                </div>
                <div class="text-sm text-gray-500">
                    Last updated: <span id="last-updated">{{ now()->format('H:i:s') }}</span>
                </div>
            </div>

            <!-- Enhanced Welcome Section -->
            <div class="@if(Auth::user()->role === 'SuperAdmin') bg-gradient-to-r from-red-600 to-red-700 @elseif(Auth::user()->role === 'Admin') bg-blue-600 @elseif(Auth::user()->role === 'Staff') bg-gradient-to-r from-green-600 to-teal-600 @else bg-gradient-to-r from-purple-600 to-pink-600 @endif overflow-hidden shadow-lg sm:rounded-lg mb-6">
                <div class="p-8 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            @if(Auth::user()->role === 'SuperAdmin')
                                <h1 class="text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                                <p class="text-red-100 text-lg mb-4">SuperAdmin Dashboard - Full System Control</p>
                                <div class="flex items-center space-x-4 text-red-100">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm">System Administrator</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M12 2l3.09 6.26L22 9l-5 4.87L18.18 22 12 18.27 5.82 22 7 13.87 2 9l6.91-.74L12 2z"></path>
                                        </svg>
                                        <span class="text-sm">Full Access</span>
                                    </div>
                                </div>
                            @elseif(Auth::user()->role === 'Admin')
                                <h1 class="text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                                <p class="text-blue-100 text-lg mb-4">Admin Dashboard - System Management & Oversight</p>
                                <div class="flex items-center space-x-4 text-blue-100">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm">Administrator</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                        </svg>
                                        <span class="text-sm">{{ $total_alumni ?? 0 }} Alumni Records</span>
                                    </div>
                                </div>
                            @elseif(Auth::user()->role === 'Staff')
                                <h1 class="text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                                <p class="text-green-100 text-lg mb-4">Staff Dashboard - Alumni Record Management</p>
                                <div class="flex items-center space-x-4 text-green-100">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                        </svg>
                                        <span class="text-sm">Staff Member</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 2a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm">{{ $my_total_submissions ?? 0 }} Submissions</span>
                                    </div>
                                </div>
                            @else
                                <h1 class="text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                                <p class="text-purple-100 text-lg mb-4">User Dashboard - Profile & Announcements</p>
                            @endif
                        </div>
                        <div class="hidden md:block">
                            <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                @if(Auth::user()->profile_picture)
                                    <img src="{{ Auth::user()->profile_picture_url }}" alt="Profile" class="w-16 h-16 rounded-full object-cover">
                                @else
                                    <div class="w-16 h-16 rounded-full bg-white bg-opacity-30 flex items-center justify-center text-white text-xl font-bold">
                                        {{ Auth::user()->initials }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions for Admin/Staff -->
                    @if(Auth::user()->role === 'Admin' || Auth::user()->role === 'Staff')
                    <div class="mt-6 flex flex-wrap gap-3">
                        @if(Auth::user()->role === 'Admin')
                            <a href="{{ route('approval.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Pending Approvals
                            </a>
                            <a href="{{ route('Alumni.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                </svg>
                                Manage Alumni
                            </a>
                            <a href="{{ route('membership.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Membership
                            </a>
                        @elseif(Auth::user()->role === 'Staff')
                            <a href="{{ route('Alumni.create') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path>
                                </svg>
                                Add Alumni
                            </a>
                            <a href="{{ route('staff.pending-changes') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 2a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                                </svg>
                                My Submissions
                            </a>
                            <a href="{{ route('Alumni.index') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                </svg>
                                View Alumni
                            </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Enhanced Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Users -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Users</p>
                                <p id="total-users" class="text-3xl font-bold text-gray-900">{{ $total_users }}</p>
                                <p class="text-xs text-gray-400 mt-1">Registered accounts</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Alumni -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">Alumni Records</p>
                                <p id="total-alumni" class="text-3xl font-bold text-gray-900">{{ $total_alumni }}</p>
                                <p class="text-xs text-gray-400 mt-1">Complete profiles</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Registrations -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">New This Week</p>
                                <p id="recent-registrations" class="text-3xl font-bold text-gray-900">{{ $recent_registrations }}</p>
                                <p class="text-xs text-gray-400 mt-1">Recent signups</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Status -->
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">System Status</p>
                                <p class="text-3xl font-bold text-green-600">Online</p>
                                <p class="text-xs text-gray-400 mt-1">All systems operational</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role-specific Statistics -->
            @if(Auth::user()->role === 'Admin')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Pending Changes</h4>
                        <p id="pending-changes" class="text-3xl font-bold text-orange-600">{{ $pending_changes }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Approved Changes</h4>
                        <p id="approved-changes" class="text-3xl font-bold text-green-600">{{ $approved_changes }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Denied Changes</h4>
                        <p id="denied-changes" class="text-3xl font-bold text-red-600">{{ $denied_changes }}</p>
                    </div>
                </div>
            </div>
            @elseif(Auth::user()->role === 'Staff')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">My Pending</h4>
                        <p id="my-pending-submissions" class="text-3xl font-bold text-orange-600">{{ $my_pending_submissions }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">My Approved</h4>
                        <p id="my-approved-submissions" class="text-3xl font-bold text-green-600">{{ $my_approved_submissions }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Total Submissions</h4>
                        <p id="my-total-submissions" class="text-3xl font-bold text-blue-600">{{ $my_total_submissions }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Alumni Analytics Charts Section -->
            @if(Auth::user()->role === 'Admin' || Auth::user()->role === 'Staff' || Auth::user()->role === 'SuperAdmin')
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg mb-8">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-lg font-semibold text-gray-900">Alumni Records & Membership Analytics</h4>
                        <div class="flex space-x-2">
                            <button id="refreshChartsBtn" class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition-colors">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>
                    
                    <!-- Chart Navigation Tabs -->
                    <div class="mb-6">
                        <nav class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                            <button class="chart-tab-btn active px-3 py-2 text-sm font-medium rounded-md transition-colors" data-chart="demographics">
                                Demographics
                            </button>
                            <button class="chart-tab-btn px-3 py-2 text-sm font-medium rounded-md transition-colors" data-chart="membership">
                                Membership
                            </button>
                            <button class="chart-tab-btn px-3 py-2 text-sm font-medium rounded-colors" data-chart="employment">
                                Employment
                            </button>
                            <button class="chart-tab-btn px-3 py-2 text-sm font-medium rounded-md transition-colors" data-chart="trends">
                                Trends
                            </button>
                            <button class="chart-tab-btn px-3 py-2 text-sm font-medium rounded-md transition-colors" data-chart="batch">
                                Batch
                            </button>
                        </nav>
                    </div>

                    <!-- Charts Container -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Demographics Charts -->
                        <div id="demographics-charts" class="chart-section">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="text-md font-semibold text-gray-800 mb-3">Gender Distribution</h5>
                                <div class="relative" style="height: 300px;">
                                    <canvas id="genderChart" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>

                        <div id="age-charts" class="chart-section">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="text-md font-semibold text-gray-800 mb-3">Age Groups</h5>
                                <div class="relative" style="height: 300px;">
                                    <canvas id="ageChart" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Membership Charts -->
                        <div id="membership-charts" class="chart-section hidden">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="text-md font-semibold text-gray-800 mb-3">Membership Status</h5>
                                <div class="relative" style="height: 300px;">
                                    <canvas id="membershipChart" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>

                        <div id="course-charts" class="chart-section">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="text-md font-semibold text-gray-800 mb-3">Alumni by Course</h5>
                                <div class="relative" style="height: 300px;">
                                    <canvas id="courseChart" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Employment Charts -->
                        <div id="employment-charts" class="chart-section hidden">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="text-md font-semibold text-gray-800 mb-3">Employment Status</h5>
                                <div class="relative" style="height: 300px;">
                                    <canvas id="employmentChart" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Trends Charts -->
                        <div id="trends-charts" class="chart-section hidden">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="text-md font-semibold text-gray-800 mb-3">Registration Trends</h5>
                                <div class="relative" style="height: 300px;">
                                    <canvas id="trendsChart" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Batch Charts -->
                        <div id="batch-charts" class="chart-section hidden">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="text-md font-semibold text-gray-800 mb-3">Alumni by Batch</h5>
                                <div class="relative" style="height: 300px;">
                                    <canvas id="batchChart" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="charts-loading" class="hidden text-center py-8">
                        <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-gray-500 bg-white">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading analytics data...
                        </div>
                    </div>

                    <!-- Error State -->
                    <div id="charts-error" class="hidden text-center py-8">
                        <div class="text-red-600">
                            <svg class="w-12 h-12 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-lg font-semibold">Failed to load analytics data</p>
                            <p class="text-sm text-gray-600 mt-2">Please try refreshing the charts</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- AI Insights Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-4 flex justify-between items-center">
                    <h5 class="text-lg font-semibold text-white mb-0 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        AI-Powered Analytics Insights
                    </h5>
                    <span class="bg-white text-gray-800 px-2 py-1 rounded text-sm flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                        </svg>
                        Claude AI
                    </span>
                </div>
                <div class="p-6">
                    @if(isset($aiInsights) && !empty($aiInsights))
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Key Trends -->
                            <div class="h-full">
                                <h6 class="text-green-600 font-semibold mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                                    </svg>
                                    Key Trends Identified
                                </h6>
                                <div id="trends-content" class="max-h-72 overflow-y-auto pr-2">
                                    @if(isset($aiInsights['trends']) && is_array($aiInsights['trends']) && count($aiInsights['trends']) > 0)
                                        @foreach($aiInsights['trends'] as $trend)
                                            <div class="flex items-start mb-3 p-3 bg-gray-50 rounded-lg">
                                                <div class="bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-3 mt-1 flex-shrink-0">{{ $loop->iteration }}</div>
                                                <span class="text-sm text-gray-800">{{ $trend }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-gray-500 text-sm">No specific trends identified at this time.</p>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Recommendations -->
                            <div class="h-full">
                                <h6 class="text-blue-600 font-semibold mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    AI Recommendations
                                </h6>
                                <div id="recommendations-content" class="max-h-72 overflow-y-auto pr-2">
                                    @if(isset($aiInsights['recommendations']) && is_array($aiInsights['recommendations']) && count($aiInsights['recommendations']) > 0)
                                        @foreach($aiInsights['recommendations'] as $recommendation)
                                            <div class="flex items-start mb-3 p-3 bg-gray-50 rounded-lg">
                                                <div class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-3 mt-1 flex-shrink-0">{{ $loop->iteration }}</div>
                                                <span class="text-sm text-gray-800">{{ $recommendation }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-gray-500 text-sm">No specific recommendations available.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Predictions Row -->
                        <div id="predictions-content" class="mt-6" style="display: none;">
                            <h6 class="text-purple-600 font-semibold mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Future Predictions
                            </h6>
                            <div id="predictions-list" class="bg-purple-50 p-4 rounded-lg">
                                @if(isset($aiInsights['predictions']) && is_array($aiInsights['predictions']) && count($aiInsights['predictions']) > 0)
                                    @foreach($aiInsights['predictions'] as $index => $prediction)
                                        <div class="flex items-start mb-2 last:mb-0">
                                            <svg class="w-4 h-4 text-purple-600 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm text-gray-800">{{ $prediction }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        
                        <!-- Anomalies Alert -->
                        <div id="anomalies-content" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-6" style="display: none;">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-600 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div id="anomalies-list">
                                    <strong class="text-yellow-800">Anomalies Detected by AI:</strong>
                                    @if(isset($aiInsights['anomalies']) && is_array($aiInsights['anomalies']) && count($aiInsights['anomalies']) > 0)
                                        @foreach($aiInsights['anomalies'] as $anomaly)
                                            <br>• {{ $anomaly }}
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Refresh Button -->
                        <div class="text-center mt-6">
                            <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200" onclick="refreshAIInsights()">
                                <svg class="w-4 h-4 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                </svg>
                                Refresh AI Analysis
                            </button>
                            <p class="text-gray-500 text-sm mt-2">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                Last updated: <span id="last-updated">{{ now()->format('M d, Y H:i') }}</span>
                            </p>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                            <h6 class="text-gray-600 font-medium">AI is analyzing your alumni data...</h6>
                            <p class="text-gray-500 text-sm mt-2">This may take a few moments</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Recent Activities</h4>
                    <div id="recent-activities">
                        @forelse($recent_activities as $activity)
                        <div class="flex items-center py-3 border-b border-gray-200 last:border-b-0">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $activity->description }}</p>
                                <p class="text-sm text-gray-500">by {{ $activity->user ? $activity->user->name : 'System' }} • {{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">No recent activities</p>
                        @endforelse
                    </div>
                </div>
            </div>



        </div>
    
    <!-- Replace the existing <style> section with this enhanced version -->
    <style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    
    .ai-insights-card {
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border-radius: 15px;
        overflow: hidden;
    }
    
    .trends-container, .recommendations-container {
        max-height: 300px;
        overflow-y: auto;
        padding-right: 10px;
    }
    
    .trends-container::-webkit-scrollbar,
    .recommendations-container::-webkit-scrollbar {
        width: 6px;
    }
    
    .trends-container::-webkit-scrollbar-track,
    .recommendations-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .trends-container::-webkit-scrollbar-thumb,
    .recommendations-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .badge.rounded-pill {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .ai-insight-item {
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        margin-bottom: 12px;
    }
    
    .ai-insight-item:hover {
        transform: translateX(8px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-left-color: #007bff;
        background: linear-gradient(90deg, rgba(0,123,255,0.05) 0%, transparent 100%);
    }
    
    .prediction-item {
        background: linear-gradient(135deg, rgba(0,123,255,0.1) 0%, rgba(0,123,255,0.05) 100%);
        border-left: 4px solid #007bff;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .prediction-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0,123,255,0.2);
    }
    
    .anomaly-alert {
        background: linear-gradient(135deg, rgba(255,193,7,0.1) 0%, rgba(255,193,7,0.05) 100%);
        border: 1px solid rgba(255,193,7,0.3);
        border-radius: 10px;
    }
    
    .refresh-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 25px;
        padding: 8px 20px;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102,126,234,0.3);
    }
    
    .refresh-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102,126,234,0.4);
        color: white;
    }
    
    .ai-header-badge {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
    }
    
    .section-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .ai-insights-card {
        animation: fadeInUp 0.6s ease-out;
    }
    
    /* AI Chatbot Styles */
    .ai-chatbot-card {
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border-radius: 15px;
        overflow: hidden;
        max-height: 600px;
        display: flex;
        flex-direction: column;
    }
    
    .chat-messages-container {
        height: 350px;
        overflow-y: auto;
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-bottom: 1px solid #e9ecef;
    }
    
    .chat-messages-container::-webkit-scrollbar {
        width: 6px;
    }
    
    .chat-messages-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .chat-messages-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .bot-message, .user-message {
        display: flex;
        margin-bottom: 15px;
        animation: fadeInUp 0.3s ease-out;
    }
    
    .user-message {
        justify-content: flex-end;
    }
    
    .message-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        flex-shrink: 0;
    }
    
    .user-message .message-avatar {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        margin-right: 0;
        margin-left: 10px;
        order: 2;
    }
    
    .message-content {
        background: white;
        padding: 12px 16px;
        border-radius: 18px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        max-width: 70%;
        position: relative;
    }
    
    .user-message .message-content {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
    }
    
    .message-content::before {
        content: '';
        position: absolute;
        top: 15px;
        left: -8px;
        width: 0;
        height: 0;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
        border-right: 8px solid white;
    }
    
    .user-message .message-content::before {
        left: auto;
        right: -8px;
        border-right: none;
        border-left: 8px solid #007bff;
    }
    
    .feature-list {
        list-style: none;
        padding: 0;
        margin: 10px 0;
    }
    
    .feature-list li {
        padding: 5px 0;
        font-size: 14px;
    }
    
    .quick-actions-container {
        padding: 15px 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }
    
    .quick-actions-header {
        margin-bottom: 10px;
        font-weight: 600;
    }
    
    .quick-actions-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .quick-action-btn {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 12px;
        color: #495057;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    
    .quick-action-btn:hover {
        background: #007bff;
        color: white;
        border-color: #007bff;
        transform: translateY(-1px);
    }
    
    .chat-input-container {
        padding: 25px 30px;
        background: white;
        border-top: 1px solid #e9ecef;
    }
    
    .chat-input {
        border: 2px solid #e9ecef;
        border-radius: 25px;
        padding: 15px 25px;
        font-size: 15px;
        height: 50px;
        transition: all 0.2s ease;
        margin-right: 15px;
    }
    
    .chat-input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        outline: none;
    }
    
    .input-group {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .send-btn {
        border-radius: 50%;
        width: 55px;
        height: 55px;
        min-width: 55px;
        min-height: 55px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        border: none;
        transition: all 0.2s ease;
        cursor: pointer;
        flex-shrink: 0;
    }
    
    .send-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(0,123,255,0.3);
    }
    
    .send-btn:active {
        transform: scale(0.95);
    }
    
    .typing-indicator {
        display: flex;
        align-items: center;
        margin-top: 10px;
        color: #6c757d;
        font-size: 12px;
    }
    
    .typing-indicator span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #6c757d;
        margin-right: 3px;
        animation: typing 1.4s infinite;
    }
    
    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }
    
    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }
    
    @keyframes typing {
        0%, 60%, 100% {
            transform: translateY(0);
        }
        30% {
            transform: translateY(-10px);
        }
    }
    
    .online-indicator {
        width: 8px;
        height: 8px;
        background: #28a745;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
    }

    /* Alumni Analytics Chart Styles */
    .chart-tab-btn {
        background-color: transparent;
        color: #6B7280;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .chart-tab-btn:hover {
        color: #374151;
        background-color: #F3F4F6;
    }

    .chart-tab-btn.active {
        background-color: #3B82F6 !important;
        color: white !important;
    }

    .chart-section {
        transition: all 0.3s ease-in-out;
    }

    .chart-container {
        position: relative;
        height: 400px;
        width: 100%;
    }

    .chart-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 400px;
        color: #6B7280;
    }

    .chart-error {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 400px;
        color: #EF4444;
        text-align: center;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }
    </style>
    
    <!-- Chatbot JavaScript -->
    <script>
    class AIChatbot {
        constructor() {
            this.chatMessages = document.getElementById('chatMessages');
            this.chatInput = document.getElementById('chatInput');
            this.sendButton = document.getElementById('sendMessage');
            this.quickActions = document.getElementById('quickActions');
            this.typingIndicator = document.getElementById('typingIndicator');
            this.sessionId = this.generateSessionId();
            
            this.init();
        }
        
        init() {
            this.loadQuickActions();
            this.bindEvents();
            this.loadChatHistory();
        }
        
        generateSessionId() {
            return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        }
        
        bindEvents() {
            this.sendButton.addEventListener('click', () => this.sendMessage());
            this.chatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.sendMessage();
                }
            });
        }
        
        async loadQuickActions() {
            try {
                const response = await fetch('/api/chatbot/common-queries', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    this.renderQuickActions(data.queries);
                }
            } catch (error) {
                console.error('Error loading quick actions:', error);
            }
        }
        
        renderQuickActions(queries) {
            this.quickActions.innerHTML = queries.map(query => 
                `<button class="quick-action-btn" onclick="chatbot.sendQuickAction('${query}')">${query}</button>`
            ).join('');
        }
        
        async loadChatHistory() {
            try {
                const response = await fetch(`/api/chatbot/history?session_id=${this.sessionId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const data = await response.json();
                
                if (data.success && data.messages.length > 0) {
                    // Clear welcome message if there's chat history
                    const welcomeMessage = this.chatMessages.querySelector('.welcome-message');
                    if (welcomeMessage) {
                        welcomeMessage.remove();
                    }
                    
                    data.messages.forEach(message => {
                        this.addMessage(message.content, message.type, false);
                    });
                }
            } catch (error) {
                console.error('Error loading chat history:', error);
            }
        }
        
        async sendMessage() {
            const message = this.chatInput.value.trim();
            if (!message) return;
            
            this.addMessage(message, 'user');
            this.chatInput.value = '';
            this.showTyping();
            
            try {
                const response = await fetch('/api/chatbot/message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message: message,
                        session_id: this.sessionId
                    })
                });
                
                const data = await response.json();
                this.hideTyping();
                
                if (data.success) {
                    this.addMessage(data.response, 'bot');
                } else {
                    this.addMessage('Sorry, I encountered an error. Please try again.', 'bot');
                }
            } catch (error) {
                this.hideTyping();
                this.addMessage('Sorry, I\'m having trouble connecting. Please try again.', 'bot');
                console.error('Error sending message:', error);
            }
        }
        
        sendQuickAction(query) {
            this.chatInput.value = query;
            this.sendMessage();
        }
        
        addMessage(content, type, animate = true) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `${type}-message`;
            
            const avatar = type === 'user' ? 
                `<div class="message-avatar">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                </div>` :
                `<div class="message-avatar">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>`;
            
            messageDiv.innerHTML = `
                ${avatar}
                <div class="message-content">
                    ${this.formatMessage(content)}
                </div>
            `;
            
            // Remove welcome message when first real message is added
            const welcomeMessage = this.chatMessages.querySelector('.welcome-message');
            if (welcomeMessage && type === 'user') {
                welcomeMessage.remove();
            }
            
            this.chatMessages.appendChild(messageDiv);
            this.scrollToBottom();
            
            if (animate) {
                messageDiv.style.opacity = '0';
                messageDiv.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    messageDiv.style.transition = 'all 0.3s ease';
                    messageDiv.style.opacity = '1';
                    messageDiv.style.transform = 'translateY(0)';
                }, 50);
            }
        }
        
        formatMessage(content) {
            // Convert line breaks to HTML
            content = content.replace(/\n/g, '<br>');
            
            // Format lists
            content = content.replace(/^• (.+)$/gm, '<li>$1</li>');
            if (content.includes('<li>')) {
                content = content.replace(/(<li>.*<\/li>)/s, '<ul>$1</ul>');
            }
            
            // Format bold text
            content = content.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            
            return content;
        }
        
        showTyping() {
            this.typingIndicator.style.display = 'flex';
            this.scrollToBottom();
        }
        
        hideTyping() {
            this.typingIndicator.style.display = 'none';
        }
        
        scrollToBottom() {
            setTimeout(() => {
                this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
            }, 100);
        }
        
        async clearHistory() {
            try {
                const response = await fetch('/api/chatbot/clear-history', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        session_id: this.sessionId
                    })
                });
                
                if (response.ok) {
                    this.chatMessages.innerHTML = `
                        <div class="welcome-message">
                            <div class="bot-message">
                                <div class="message-avatar">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="message-content">
                                    <p>Chat history cleared! How can I help you today?</p>
                                </div>
                            </div>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error clearing history:', error);
            }
        }
    }
    
    // Initialize chatbot when page loads
    let chatbot;
    document.addEventListener('DOMContentLoaded', function() {
        chatbot = new AIChatbot();
        
        // Show predictions and anomalies sections if they have data
        const predictionsContainer = document.querySelector('#predictions-content');
        const predictionsList = document.querySelector('#predictions-list');
        if (predictionsContainer && predictionsList && predictionsList.children.length > 0) {
            predictionsContainer.style.display = 'block';
        }
        
        const anomaliesContainer = document.querySelector('#anomalies-content');
        const anomaliesList = document.querySelector('#anomalies-list');
        if (anomaliesContainer && anomaliesList && anomaliesList.innerHTML.includes('•')) {
            anomaliesContainer.style.display = 'block';
        }
    });
    
    // Refresh AI Insights function
    async function refreshAIInsights() {
        const button = event.target;
        const originalText = button.innerHTML;
        
        // Show loading state
        button.disabled = true;
        button.innerHTML = `
            <svg class="w-4 h-4 inline mr-2 animate-spin" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
            </svg>
            Refreshing...
        `;
        
        try {
            const response = await fetch('/dashboard/refresh-ai-insights', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                // Update the AI insights content directly without page reload
                updateAIInsightsContent(data.data);
                
                // Show success message
                showNotification('AI insights refreshed successfully!', 'success');
                
                // Update the timestamp
                const timestampElement = document.querySelector('.ai-insights-timestamp');
                if (timestampElement) {
                    const now = new Date();
                    timestampElement.textContent = `Last updated: ${now.toLocaleDateString()} ${now.toLocaleTimeString()}`;
                }
            } else {
                throw new Error(data.error || 'Failed to refresh insights');
            }
        } catch (error) {
            console.error('Error refreshing AI insights:', error);
            showNotification('Failed to refresh AI insights. Please try again.', 'error');
        } finally {
            // Restore button state
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }
    
    // Function to update AI insights content dynamically
    function updateAIInsightsContent(insights) {
        // Update trends
        const trendsContainer = document.querySelector('#trends-content');
        if (trendsContainer && insights.trends) {
            trendsContainer.innerHTML = insights.trends.length > 0 
                ? insights.trends.map((trend, index) => `
                    <div class="flex items-start mb-3 p-3 bg-gray-50 rounded-lg">
                        <div class="bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-3 mt-1 flex-shrink-0">${index + 1}</div>
                        <span class="text-sm text-gray-800">${trend}</span>
                    </div>
                `).join('')
                : '<p class="text-gray-500 text-sm">No specific trends identified at this time.</p>';
        }
        
        // Update recommendations
        const recommendationsContainer = document.querySelector('#recommendations-content');
        if (recommendationsContainer && insights.recommendations) {
            recommendationsContainer.innerHTML = insights.recommendations.length > 0
                ? insights.recommendations.map((rec, index) => `
                    <div class="flex items-start mb-3 p-3 bg-gray-50 rounded-lg">
                        <div class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-semibold mr-3 mt-1 flex-shrink-0">${index + 1}</div>
                        <span class="text-sm text-gray-800">${rec}</span>
                    </div>
                `).join('')
                : '<p class="text-gray-500 text-sm">No specific recommendations available.</p>';
        }
        
        // Update predictions
        const predictionsContainer = document.querySelector('#predictions-content');
        const predictionsList = document.querySelector('#predictions-list');
        if (predictionsContainer && predictionsList && insights.predictions) {
            if (insights.predictions.length > 0) {
                predictionsList.innerHTML = insights.predictions.map(pred => `
                    <div class="flex items-start mb-2 last:mb-0">
                        <svg class="w-4 h-4 text-purple-600 mr-3 mt-1 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm text-gray-800">${pred}</span>
                    </div>
                `).join('');
                predictionsContainer.style.display = 'block';
            } else {
                predictionsContainer.style.display = 'none';
            }
        }
        
        // Update anomalies
        const anomaliesContainer = document.querySelector('#anomalies-content');
        const anomaliesList = document.querySelector('#anomalies-list');
        if (anomaliesContainer && anomaliesList && insights.anomalies) {
            if (insights.anomalies.length > 0) {
                anomaliesList.innerHTML = `
                    <strong class="text-yellow-800">Anomalies Detected by AI:</strong>
                    ${insights.anomalies.map(anomaly => `<br>• ${anomaly}`).join('')}
                `;
                anomaliesContainer.style.display = 'block';
            } else {
                anomaliesContainer.style.display = 'none';
            }
        }
        
        // Update timestamp
        const timestampElement = document.querySelector('#last-updated');
        if (timestampElement) {
            const now = new Date();
            const options = { 
                year: 'numeric', 
                month: 'short', 
                day: '2-digit', 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false
            };
            timestampElement.textContent = now.toLocaleDateString('en-US', options).replace(',', '');
        }
    }
    
    // Show notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-md shadow-lg ${
            type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }



    // Alumni Analytics Charts Implementation
    let analyticsCharts = {};
    let currentActiveTab = 'demographics';

    // Chart color schemes
    const chartColors = {
        primary: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#84CC16', '#F97316'],
        gender: ['#3B82F6', '#EC4899', '#6B7280'],
        age: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'],
        employment: ['#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
        membership: ['#10B981', '#F59E0B', '#EF4444'],
        trends: '#3B82F6'
    };

    // Initialize analytics charts
    function initializeAnalyticsCharts() {
        console.log('Initializing analytics charts...');
        
        if (typeof Chart === 'undefined') {
            console.error('Chart.js not loaded');
            showChartsError();
            return;
        }
        
        console.log('Chart.js loaded successfully, version:', Chart.version);
        showChartsLoading();
        fetchAnalyticsData();
    }

    // Fetch analytics data from API
    function fetchAnalyticsData() {
        console.log('Fetching analytics data from API...');
        
        // Add timeout to fetch request
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 second timeout
        
        fetch('/api/dashboard/alumni-chart-data?type=all', {
            signal: controller.signal,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                clearTimeout(timeoutId);
                console.log('API response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Analytics data received:', data);
                if (data.success && data.data) {
                    console.log('Data is valid, creating charts...');
                    createAllCharts(data.data);
                    hideChartsLoading();
                } else {
                    console.error('API returned error:', data.error || 'Invalid data structure');
                    // Try fallback data
                    tryFallbackData();
                }
            })
            .catch(error => {
                clearTimeout(timeoutId);
                console.error('Analytics data fetch error:', error);
                
                if (error.name === 'AbortError') {
                    console.error('Request timed out');
                }
                
                // Try fallback data
                tryFallbackData();
            });
    }
    
    // Fallback data function
    function tryFallbackData() {
        console.log('Trying fallback data...');
        
        // Create sample data structure
        const fallbackData = {
            gender: {
                labels: ['Male', 'Female', 'Other'],
                data: [0, 0, 0]
            },
            age: {
                labels: ['18-24', '25-34', '35-44', '45-54', '55+'],
                data: [0, 0, 0, 0, 0]
            },
            course: {
                labels: ['No Data'],
                data: [0]
            },
            membership: {
                labels: ['No Data'],
                data: [0]
            },
            employment: {
                labels: ['No Data'],
                data: [0]
            },
            trends: {
                labels: ['No Data'],
                data: [0]
            },
            batch: {
                labels: ['No Data'],
                data: [0]
            }
        };
        
        console.log('Using fallback data:', fallbackData);
        createAllCharts(fallbackData);
        hideChartsLoading();
        
        // Show a warning message
        const chartsContainer = document.getElementById('charts-container');
        if (chartsContainer) {
            const warningDiv = document.createElement('div');
            warningDiv.className = 'bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4';
            warningDiv.innerHTML = '<strong>Warning:</strong> Unable to load chart data. Displaying empty charts. Please refresh the page or contact support.';
            chartsContainer.insertBefore(warningDiv, chartsContainer.firstChild);
        }
    }

    // Create all charts with fetched data
    function createAllCharts(data) {
        console.log('Creating all charts with data:', data);
        
        // Destroy existing charts
        Object.values(analyticsCharts).forEach(chart => {
            if (chart && typeof chart.destroy === 'function') {
                try {
                    chart.destroy();
                } catch (e) {
                    console.warn('Error destroying chart:', e);
                }
            }
        });
        analyticsCharts = {};

        // Validate data structure
        if (!data || typeof data !== 'object') {
            console.error('Invalid data structure:', data);
            showChartsError();
            return;
        }

        // Create individual charts with error handling
        const chartCreators = [
            { name: 'gender', creator: createGenderChart, data: data.gender },
            { name: 'age', creator: createAgeChart, data: data.age },
            { name: 'course', creator: createCourseChart, data: data.course },
            { name: 'membership', creator: createMembershipChart, data: data.membership },
            { name: 'employment', creator: createEmploymentChart, data: data.employment },
            { name: 'trends', creator: createTrendsChart, data: data.trends },
            { name: 'batch', creator: createBatchChart, data: data.batch }
        ];
        
        let successCount = 0;
        
        chartCreators.forEach(({ name, creator, data: chartData }) => {
            try {
                if (chartData && validateChartData(chartData)) {
                    console.log(`Creating ${name} chart...`);
                    creator(chartData);
                    successCount++;
                } else {
                    console.warn(`Skipping ${name} chart - invalid data:`, chartData);
                }
            } catch (error) {
                console.error(`Error creating ${name} chart:`, error);
            }
        });
        
        console.log(`Successfully created ${successCount} out of ${chartCreators.length} charts`);
        
        if (successCount === 0) {
            showChartsError();
        }
    }
    
    // Validate chart data structure
    function validateChartData(data) {
        return data && 
               typeof data === 'object' && 
               Array.isArray(data.labels) && 
               Array.isArray(data.data) && 
               data.labels.length > 0 && 
               data.data.length > 0 && 
               data.labels.length === data.data.length;
    }

    // Individual chart creation functions
    function createGenderChart(data) {
        const ctx = document.getElementById('genderChart');
        if (!ctx) {
            console.error('Gender chart canvas not found');
            return;
        }
        
        // Validate canvas context
        const context = ctx.getContext('2d');
        if (!context) {
            console.error('Cannot get 2D context for gender chart');
            return;
        }

        try {
            // Ensure we have valid data
            const chartData = {
                labels: data.labels || ['No Data'],
                data: data.data || [0]
            };
            
            analyticsCharts.gender = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartData.labels,
                datasets: [{
                    data: chartData.data,
                    backgroundColor: chartColors.gender.slice(0, chartData.labels.length),
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        } catch (error) {
            console.error('Error creating gender chart:', error);
        }
    }

    function createAgeChart(data) {
        const ctx = document.getElementById('ageChart');
        if (!ctx) {
            console.error('Age chart canvas not found');
            return;
        }

        try {
            analyticsCharts.age = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Alumni Count',
                    data: data.data,
                    backgroundColor: chartColors.age,
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed.y / total) * 100).toFixed(1);
                                return `${context.dataset.label}: ${context.parsed.y} (${percentage}%)`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        } catch (error) {
            console.error('Error creating age chart:', error);
        }
    }

    function createCourseChart(data) {
        const ctx = document.getElementById('courseChart');
        if (!ctx) {
            console.error('Course chart canvas not found');
            return;
        }

        try {
            analyticsCharts.course = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Alumni Count',
                    data: data.data,
                    backgroundColor: chartColors.primary,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed.x / total) * 100).toFixed(1);
                                return `${context.dataset.label}: ${context.parsed.x} (${percentage}%)`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        } catch (error) {
            console.error('Error creating course chart:', error);
        }
    }

    function createMembershipChart(data) {
        const ctx = document.getElementById('membershipChart');
        if (!ctx) {
            console.error('Membership chart canvas not found');
            return;
        }

        try {
            analyticsCharts.membership = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: chartColors.membership,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        } catch (error) {
            console.error('Error creating membership chart:', error);
        }
    }

    function createEmploymentChart(data) {
        const ctx = document.getElementById('employmentChart');
        if (!ctx) {
            console.error('Employment chart canvas not found');
            return;
        }

        try {
            analyticsCharts.employment = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: chartColors.employment,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        } catch (error) {
            console.error('Error creating employment chart:', error);
        }
    }

    function createTrendsChart(data) {
        const ctx = document.getElementById('trendsChart');
        if (!ctx) {
            console.error('Trends chart canvas not found');
            return;
        }

        try {
            analyticsCharts.trends = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'New Alumni Registrations',
                    data: data.data,
                    borderColor: chartColors.trends,
                    backgroundColor: chartColors.trends + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        } catch (error) {
            console.error('Error creating trends chart:', error);
        }
    }

    function createBatchChart(data) {
        const ctx = document.getElementById('batchChart');
        if (!ctx) {
            console.error('Batch chart canvas not found');
            return;
        }

        try {
            analyticsCharts.batch = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Alumni Count',
                    data: data.data,
                    backgroundColor: '#3B82F6',
                    borderColor: '#1D4ED8',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed.y / total) * 100).toFixed(1);
                                return `${context.dataset.label}: ${context.parsed.y} (${percentage}%)`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45
                        }
                    }
                }
            }
        });
        } catch (error) {
            console.error('Error creating batch chart:', error);
        }
    }

    // Tab switching functionality
    function switchChartTab(tabName) {
        // Update active tab
        document.querySelectorAll('.chart-tab-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white');
            btn.classList.add('bg-transparent', 'text-gray-600', 'hover:text-gray-900');
        });
        
        const activeBtn = document.querySelector(`[data-chart="${tabName}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active', 'bg-blue-600', 'text-white');
            activeBtn.classList.remove('bg-transparent', 'text-gray-600', 'hover:text-gray-900');
        }

        // Show/hide chart sections
        document.querySelectorAll('.chart-section').forEach(section => {
            section.classList.add('hidden');
        });

        // Show relevant charts based on tab
        switch(tabName) {
            case 'demographics':
                document.getElementById('demographics-charts')?.classList.remove('hidden');
                document.getElementById('age-charts')?.classList.remove('hidden');
                break;
            case 'membership':
                document.getElementById('membership-charts')?.classList.remove('hidden');
                document.getElementById('course-charts')?.classList.remove('hidden');
                break;
            case 'employment':
                document.getElementById('employment-charts')?.classList.remove('hidden');
                break;
            case 'trends':
                document.getElementById('trends-charts')?.classList.remove('hidden');
                break;
            case 'batch':
                document.getElementById('batch-charts')?.classList.remove('hidden');
                break;
        }

        currentActiveTab = tabName;
    }

    // Utility functions
    function showChartsLoading() {
        const loadingDiv = document.getElementById('charts-loading');
        const errorDiv = document.getElementById('charts-error');
        const chartsDiv = document.getElementById('charts-container');
        
        if (loadingDiv) loadingDiv.style.display = 'block';
        if (errorDiv) errorDiv.style.display = 'none';
        if (chartsDiv) chartsDiv.style.display = 'none';
    }

    function hideChartsLoading() {
        const loadingDiv = document.getElementById('charts-loading');
        const errorDiv = document.getElementById('charts-error');
        const chartsDiv = document.getElementById('charts-container');
        
        if (loadingDiv) loadingDiv.style.display = 'none';
        if (errorDiv) errorDiv.style.display = 'none';
        if (chartsDiv) chartsDiv.style.display = 'block';
    }

    function showChartsError() {
        const loadingDiv = document.getElementById('charts-loading');
        const errorDiv = document.getElementById('charts-error');
        const chartsDiv = document.getElementById('charts-container');
        
        if (loadingDiv) loadingDiv.style.display = 'none';
        if (errorDiv) errorDiv.style.display = 'block';
        if (chartsDiv) chartsDiv.style.display = 'none';
    }

    // Wait for Chart.js to load before initializing
    function waitForChartJS(callback, maxAttempts = 100, attempt = 1) {
        console.log(`Waiting for Chart.js - Attempt ${attempt}/${maxAttempts}`);
        
        if (typeof Chart !== 'undefined' && Chart.version) {
            console.log('Chart.js is loaded, version:', Chart.version);
            // Add small delay to ensure DOM is ready
            setTimeout(() => {
                if (callback) {
                    callback();
                } else {
                    initializeAnalyticsCharts();
                }
            }, 100);
        } else if (attempt < maxAttempts) {
            setTimeout(() => waitForChartJS(callback, maxAttempts, attempt + 1), 200);
        } else {
            console.error('Chart.js failed to load after', maxAttempts, 'attempts');
            console.error('Chart object:', typeof Chart);
            
            // Try to reload Chart.js
            const script = document.createElement('script');
            script.src = '{{ asset("js/chart.min.js") }}';
            script.onload = function() {
                console.log('Chart.js reloaded, retrying initialization...');
                setTimeout(() => initializeAnalyticsCharts(), 500);
            };
            script.onerror = function() {
                console.error('Failed to reload Chart.js');
                showChartsError();
            };
            document.head.appendChild(script);
        }
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize analytics charts after Chart.js loads
        waitForChartJS();

        // Tab switching
        document.querySelectorAll('.chart-tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const chartType = this.getAttribute('data-chart');
                switchChartTab(chartType);
            });
        });

        // Refresh button
        document.getElementById('refreshChartsBtn')?.addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<svg class="animate-spin w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"></path></svg>Refreshing...';
            
            fetchAnalyticsData();
            
            setTimeout(() => {
                this.disabled = false;
                this.innerHTML = '<svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path></svg>Refresh';
            }, 2000);
        });
    });

    </script>


</x-admin-layout>