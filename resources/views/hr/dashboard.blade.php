@php
    // Role protection at view level
    if (!Auth::user()->isHR()) {
        abort(403, 'Unauthorized access. HR role required.');
    }
@endphp

<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('HR Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 overflow-hidden shadow-lg sm:rounded-lg mb-6">
                <div class="p-8 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                            <p class="text-purple-100 text-lg mb-4">HR Dashboard - Announcement Management</p>
                            <div class="flex items-center space-x-4 text-purple-100">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                    </svg>
                                    <span class="text-sm">HR Representative</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"></path>
                                    </svg>
                                    <span class="text-sm">{{ $pendingAnnouncements ?? 0 }} Pending Announcements</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Create Announcement</h4>
                        <p class="text-gray-600 mb-4">Create new announcements for alumni</p>
                        <a href="{{ route('hr.announcement.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                            Create New
                        </a>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Pending Announcements</h4>
                        <p class="text-3xl font-bold text-orange-600">{{ $pendingAnnouncements ?? 0 }}</p>
                        <a href="{{ route('hr.announcement.pending') }}" class="text-orange-600 hover:text-orange-800 text-sm">View Pending →</a>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Approved Announcements</h4>
                        <p class="text-3xl font-bold text-green-600">{{ $approvedAnnouncements ?? 0 }}</p>
                        <a href="{{ route('hr.announcements') }}" class="text-green-600 hover:text-green-800 text-sm">View All →</a>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Job Opportunities</h4>
                        <p class="text-gray-600 mb-4">View all approved job opportunities</p>
                        <a href="{{ route('hr.job-opportunities') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            View Jobs
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Announcements -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h4 class="text-lg font-semibold mb-4">Recent Announcements</h4>
                    @if(isset($announcements) && $announcements->count() > 0)
                        <div class="space-y-4">
                            @foreach($announcements->take(5) as $announcement)
                                <div class="border-l-4 @if($announcement->status === 'approved') border-green-500 @else border-orange-500 @endif pl-4 py-2">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h5 class="font-medium text-gray-900">{{ $announcement->title }}</h5>
                                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit(strip_tags($announcement->content), 100) }}</p>
                                            <p class="text-xs text-gray-500 mt-2">Created {{ $announcement->created_at->diffForHumans() }}</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            @if($announcement->status === 'approved') bg-green-100 text-green-800 
                                            @else bg-orange-100 text-orange-800 @endif">
                                            {{ ucfirst($announcement->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No announcements available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>