<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('Images/bantay.jpg') }}') !important; background-size: cover !important; background-position: center !important; background-repeat: no-repeat !important; background-attachment: fixed !important; min-height: 100vh !important;">
        <div class="relative min-h-screen" x-data="{ open: true, darkMode: false }">
           <!-- Sidebar -->
<aside 
:class="open ? 'w-64' : 'w-0 md:w-0'" 
class="bg-blue-800 text-blue-100 transform transition-all duration-300 ease-in-out shadow-lg fixed top-0 left-0 h-full z-50 overflow-hidden"
>
<!-- Sidebar Content -->
<div x-show="open" x-transition.opacity.delay.200ms class="absolute top-0 left-0 w-64 px-2 py-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
            @if(Auth::user()->profile_picture)
                <img src="{{ Auth::user()->profile_picture_url }}" alt="Profile" class="w-10 h-10 rounded-full object-cover">
            @else
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white text-lg font-semibold">
                    {{ Auth::user()->initials }}
                </div>
            @endif
            <div class="flex flex-col">
                <span class="text-lg font-bold text-blue-100">{{ Auth::user()->name }}</span>
                <span class="text-sm text-blue-300">
                    @if(Auth::user()->role === 'Admin')
                        Admin
                    @elseif(Auth::user()->role === 'Staff')
                        Staff
                    @else
                        Dashboard
                    @endif
                </span>
            </div>
        </div>
        <!-- Close Button -->
        <button @click="open = false" class="p-2 rounded-md text-blue-100 hover:bg-blue-700 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Nav Links -->
    <nav class="mt-4">
        @if(Auth::user()->role === 'HR')
            {{-- HR Navigation --}}
            <x-side-nav-link href="{{ route('hr.dashboard') }}" :active="request()->routeIs('hr.dashboard')">
                Dashboard
            </x-side-nav-link>
            <x-side-nav-link href="{{ route('hr.announcements') }}" :active="request()->routeIs('hr.announcements')">
                View Announcements
            </x-side-nav-link>
            <x-side-nav-link href="{{ route('hr.announcement.create') }}" :active="request()->routeIs('hr.announcement.create')">
                Create Announcement
            </x-side-nav-link>
            <x-side-nav-link href="{{ route('hr.announcement.pending') }}" :active="request()->routeIs('hr.announcement.pending')">
                Pending Announcements
            </x-side-nav-link>
            <x-side-nav-link href="{{ route('hr.job-opportunities') }}" :active="request()->routeIs('hr.job-opportunities')">
                View Job Opportunities
            </x-side-nav-link>
            <x-side-nav-link href="{{ route('hr.job-opportunity') }}" :active="request()->routeIs('hr.job-opportunity')">
                <i class="fas fa-briefcase mr-3"></i>
                {{ __('Job Opportunity') }}
            </x-side-nav-link>
            <x-side-nav-link href="{{ route('hr.job-posting.pending') }}" :active="request()->routeIs('hr.job-posting.pending')">
                Pending Job Postings
            </x-side-nav-link>
        @else
            {{-- Admin/Staff/SuperAdmin Navigation --}}
            <x-side-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-side-nav-link>
            <x-side-nav-link href="{{ route('Alumni.index') }}" :active="request()->routeIs('Alumni.index')">
                Alumni
            </x-side-nav-link>
            <x-side-nav-link href="{{ route('announcement') }}" :active="request()->routeIs('announcement')">
            Announcement
            </x-side-nav-link>
            <x-side-nav-link href="{{ route('AccountManagement.index') }}" :active="request()->routeIs('AccountManagement.index')">
                Account Records
            </x-side-nav-link>
            <x-side-nav-link :href="route('job-opportunity')" :active="request()->routeIs('job-opportunity')">
                <i class="fas fa-briefcase mr-3"></i>
                {{ __('Job Opportunity') }}
            </x-side-nav-link>

            @if(in_array(Auth::user()->role, ['Admin', 'SuperAdmin']))
                @php
                    $adminSidebarPendingCount = \App\Models\PendingChange::pending()->count();
                    $hrApprovalCount = \App\Models\Announcement::where('status', 'pending')->whereHas('user', function($q) { $q->where('role', 'HR'); })->count();
                @endphp
                
                <!-- Collapsible Approval Section -->
                <div x-data="{ approvalOpen: false }" class="relative">
                    <!-- Main Approval Button -->
                    <button @click="approvalOpen = !approvalOpen" 
                            class="w-full flex items-center justify-between px-4 py-2 text-left text-blue-100 hover:bg-blue-700 focus:outline-none transition-colors duration-200 rounded-md">
                        <div class="flex items-center justify-between w-full">
                            <span>Approval</span>
                            <div class="flex items-center space-x-2">
                                @if($adminSidebarPendingCount > 0 || $hrApprovalCount > 0)
                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                        {{ $adminSidebarPendingCount + $hrApprovalCount }}
                                    </span>
                                @endif
                                <!-- Chevron Icon -->
                                <svg :class="approvalOpen ? 'rotate-180' : ''" 
                                     class="w-4 h-4 transition-transform duration-200" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </button>
                    
                    <!-- Collapsible Sub-menu -->
                    <div x-show="approvalOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="ml-4 mt-1 space-y-1">
                        
                        <!-- Pending Changes Sub-item -->
                        <x-side-nav-link href="{{ route('approval.index') }}" :active="request()->routeIs('approval.index')" class="text-sm">
                            <div class="flex items-center justify-between w-full">
                                <span>Pending Changes</span>
                                @if($adminSidebarPendingCount > 0)
                                    <span id="admin-sidebar-badge" class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">{{ $adminSidebarPendingCount }}</span>
                                @endif
                            </div>
                        </x-side-nav-link>
                        
                        <!-- HR Approvals Sub-item -->
                        <x-side-nav-link href="{{ route('admin.hr-approvals') }}" :active="request()->routeIs('admin.hr-approvals')" class="text-sm">
                            <div class="flex items-center justify-between w-full">
                                <span>HR Approvals</span>
                                @if($hrApprovalCount > 0)
                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-purple-600 rounded-full">{{ $hrApprovalCount }}</span>
                                @endif
                            </div>
                        </x-side-nav-link>
                    </div>
                </div>
            @endif
            
            @if(Auth::user()->role === 'Staff')
                @php
                    $staffSidebarPendingCount = \App\Models\PendingChange::where('staff_user_id', Auth::id())->pending()->count();
                @endphp
                <x-side-nav-link href="{{ route('staff.pending-changes') }}" :active="request()->routeIs('staff.pending-changes')">
                    <div class="flex items-center justify-between w-full">
                        <span>Pending Changes</span>
                        @if($staffSidebarPendingCount > 0)
                            <span id="staff-sidebar-badge" class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-yellow-600 rounded-full">{{ $staffSidebarPendingCount }}</span>
                        @else
                            <span id="staff-sidebar-badge" class="hidden inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-yellow-600 rounded-full">0</span>
                        @endif
                    </div>
                </x-side-nav-link>
            @endif

            <x-side-nav-link href="{{ route('activity-logs.index') }}" :active="request()->routeIs('activity-logs.*')">
                Activity Logs
            </x-side-nav-link>

            <x-side-nav-link href="{{ route('Sendmail') }}" :active="request()->routeIs('Sendmail')">
                Sendmail
            </x-side-nav-link>

            <x-side-nav-link href="{{ route('membership.index') }}" :active="request()->routeIs('membership.*')">
                Membership
            </x-side-nav-link>
            
            <x-side-nav-link href="{{ route('admin.balanceupdate.index') }}" :active="request()->routeIs('admin.balanceupdate.*')">
                Balance Update
            </x-side-nav-link>

            <x-side-nav-link href="{{ route('admin.feedback.index') }}" :active="request()->routeIs('admin.feedback.*')">
                Feedback
            </x-side-nav-link>

            <x-side-nav-link href="{{ route('admin.alumni-concerns.index') }}" :active="request()->routeIs('admin.alumni-concerns.*')">
                Alumni Concerns
            </x-side-nav-link>

            <x-side-nav-link href="{{ route('reports.index') }}" :active="request()->routeIs('reports.*')">
                Reports
            </x-side-nav-link>

             <x-side-nav-link href="{{ route('students.index') }}" :active="request()->routeIs('students.*')">
                Students
            </x-side-nav-link>
        @endif
    </nav>
</div>
</aside>


        
            <!-- Main Content -->
            <div class="transition-all duration-300" :class="open ? 'ml-64' : 'ml-0'">
                <nav class="bg-blue-900 shadow-lg sticky top-0 z-40">
                    <div class="mx-auto px-2 sm:px-6 lg:px-8">
                        <div class="relative flex items-center justify-between md:justify-end h-16">
                            <div class="absolute inset-y-0 left-0 flex items-center">

                                <!-- Sidebar Toggle Button -->
                                <button @click="open = !open" class="p-2 rounded-md text-blue-100 hover:bg-blue-700 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                    </svg>
                                </button>
                            </div>
                            <div class="absolute inset-y-0 right-0 flex items-center space-x-4">
                                <!-- Notification Dropdown -->
                                <div class="relative" x-data="{ 
                                    open: false, 
                                    count: 0, 
                                    updates: [],
                                    async init() {
                                        console.log('Initializing notification component...');
                                        try {
                                            this.count = await window.loadNotificationCount();
                                            this.updates = await window.loadProfileUpdates();
                                            console.log('Initialization complete. Count:', this.count, 'Updates:', this.updates.length);
                                        } catch (error) {
                                            console.error('Error during initialization:', error);
                                        }
                                    },
                                    async refresh() {
                                        console.log('Refreshing notifications...');
                                        try {
                                            // Mark notifications as read when user clicks the bell
                                            await window.markNotificationsAsRead();
                                            
                                            // Reload the notifications and count
                                            this.count = await window.loadNotificationCount();
                                            this.updates = await window.loadProfileUpdates();
                                        } catch (error) {
                                            console.error('Error during refresh:', error);
                                        }
                                    }
                                }">
                                    <!-- Notification Button -->
                                    <button @click="open = !open; refresh()" class="relative p-2 text-blue-100 hover:bg-blue-700 rounded-md focus:outline-none transition ease-in-out duration-200">
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C10.9 2 10 2.9 10 4C10 4.74 10.4 5.39 11 5.73V7C7.69 7.13 5 9.84 5 13.2V16L3 18V19H21V18L19 16V13.2C19 9.84 16.31 7.13 13 7V5.73C13.6 5.39 14 4.74 14 4C14 2.9 13.1 2 12 2ZM10 20C10 21.1 10.9 22 12 22C13.1 22 14 21.1 14 20H10Z"/>
                                        </svg>
                                        <!-- Notification Badge -->
                                        <span x-show="count > 0" x-text="count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium"></span>
                                    </button>

                                    <!-- Notification Dropdown Panel -->
                                    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-[9999]" style="display: none;">
                                        <!-- Header -->
                                        <div class="px-4 py-3 border-b border-gray-200">
                                            <h3 class="text-lg font-semibold text-gray-900">Recent Profile Updates</h3>
                                        </div>

                                        <!-- Content -->
                                        <div class="max-h-96 overflow-y-auto">
                                            <!-- Loading State -->
                                            <div x-show="updates.length === 0" class="flex flex-col items-center justify-center py-8 px-4">
                                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-3.5-3.5a1.5 1.5 0 010-2.12L20 8h-5M9 17H4l3.5-3.5a1.5 1.5 0 000-2.12L4 8h5m6 9a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <p class="text-gray-500 text-center">No recent updates</p>
                                                <p class="text-gray-400 text-sm text-center mt-1">Check back later for new notifications</p>
                                            </div>

                                            <!-- Notification Items -->
                                            <template x-for="(update, index) in updates" :key="index">
                                                <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                                    <div class="flex items-start space-x-3">
                                                        <!-- User Icon -->
                                                        <div class="flex-shrink-0">
                                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                                                </svg>
                                                            </div>
                                                        </div>
                                                        <!-- Content -->
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900" x-text="update.user_name"></p>
                                                            <p class="text-xs text-gray-600 mt-1" x-text="update.description"></p>
                                                            <p class="text-xs text-gray-500 mt-1" x-text="update.updated_at"></p>
                                                        </div>
                                                        <!-- Blue Dot -->
                                                        <div class="flex-shrink-0">
                                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Profile Dropdown -->
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium text-blue-100 hover:bg-blue-700 focus:outline-none transition ease-in-out duration-200 p-2 rounded-md">
                                            @if(Auth::user()->profile_picture)
                                                <img src="{{ Auth::user()->profile_picture_url }}" alt="Profile" class="w-8 h-8 rounded-full mr-2 object-cover">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center mr-2 text-white text-sm font-semibold">
                                                    {{ Auth::user()->initials }}
                                                </div>
                                            @endif
                                            <div>{{ Auth::user()->name }}</div>
                
                                            <div class="ms-1">
                                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    </x-slot>
                
                                    <x-slot name="content">
                                        <x-dropdown-link :href="route('profile.edit')">
                                            {{ __('Profile') }}
                                        </x-dropdown-link>
                
                                        <!-- Authentication -->
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                
                                            <x-dropdown-link :href="route('logout')"
                                                    onclick="event.preventDefault();
                                                                this.closest('form').submit();">
                                                {{ __('Log Out') }}
                                            </x-dropdown-link>
                                        </form>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        </div>
                        
                    </div>
                </nav>
                
                <!-- Page Header -->
                @isset($header)
                    <header class="bg-white shadow sticky top-16 z-30">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset
                
                <!-- Global Pending Changes Notification -->
                @if(Auth::user()->role === 'Admin')
                    @php
                        $globalPendingCount = \App\Models\PendingChange::pending()->count();
                    @endphp
                    <div id="admin-pending-notification" class="{{ $globalPendingCount > 0 ? '' : 'hidden' }} bg-red-50 border-l-4 border-red-400 p-4 mx-4 mt-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    <strong id="admin-pending-count">{{ $globalPendingCount }}</strong> <span id="admin-pending-text">pending change{{ $globalPendingCount > 1 ? 's' : '' }}</span> waiting for your approval.
                                    <a href="{{ route('approval.index') }}" class="font-medium underline text-red-700 hover:text-red-600">
                                        Review now
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif(Auth::user()->role === 'Staff')
                    @php
                        $myGlobalPendingCount = \App\Models\PendingChange::where('staff_user_id', Auth::id())->pending()->count();
                    @endphp
                    <div id="staff-pending-notification" class="{{ $myGlobalPendingCount > 0 ? '' : 'hidden' }} bg-yellow-50 border-l-4 border-yellow-400 p-4 mx-4 mt-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    You have <strong id="staff-pending-count">{{ $myGlobalPendingCount }}</strong> <span id="staff-pending-text">pending change{{ $myGlobalPendingCount > 1 ? 's' : '' }}</span> awaiting approval.
                                    <a href="{{ route('staff.pending-changes') }}" class="font-medium underline text-yellow-700 hover:text-yellow-600">
                                        View status
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                

                
                <div style="min-height: calc(100vh - 64px);">
                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot ?? '' }}
                    @endif
                </div>
            </div>
        </div>
        

        <!-- JavaScript for Notifications -->
        <script>
            // Function to mark notifications as read
            window.markNotificationsAsRead = async function() {
                try {
                    console.log('Marking notifications as read...');
                    const response = await fetch('/api/profile-updates/mark-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    console.log('Mark as read response:', data);
                    return data;
                } catch (error) {
                    console.error('Error marking notifications as read:', error);
                    throw error;
                }
            };

            // Global notification functions for Alpine.js
            window.loadNotificationCount = function() {
                console.log('Loading notification count...');
                return fetch('/api/profile-updates-count')
                    .then(response => {
                        console.log('Count response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Count data received:', data);
                        return data.count || 0;
                    })
                    .catch(error => {
                        console.error('Error loading notification count:', error);
                        return 0;
                    });
            };

            window.loadProfileUpdates = function() {
                console.log('Loading profile updates...');
                return fetch('/api/profile-updates')
                    .then(response => {
                        console.log('Updates response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Updates data received:', data);
                        return data.updates || [];
                    })
                    .catch(error => {
                        console.error('Error loading updates:', error);
                        return [];
                    });
            };
        </script>

    </div>
    

</body>
</html>
