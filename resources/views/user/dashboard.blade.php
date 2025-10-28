@extends('layouts.user')

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Enhanced Welcome Section with Alumni Details -->
            <div class="bg-blue-600 overflow-hidden shadow-lg sm:rounded-lg mb-6">
                <div class="p-8 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold mb-2">Welcome back, {{ $user->name }}!</h1>
                            <p class="text-blue-100 text-lg">Alumni Management System Dashboard</p>
                        </div>
                        <div class="hidden md:block">
                            <div class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                @if($user->profile_picture)
                                    <img src="{{ $user->profile_picture_url }}" alt="Profile" class="w-16 h-16 rounded-full object-cover">
                                @else
                                    <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Join Our Alumni Community Section -->
            <div class="mt-8 bg-blue-600 rounded-lg shadow-lg p-8 text-white text-center mb-6">
                <h3 class="text-2xl font-bold mb-4">Join Our Alumni Community Today!</h3>
                <p class="text-lg mb-6">Stay connected with your fellow alumni.</p>
                <div class="space-x-4">
                    <a href="https://www.facebook.com/BestlinkAlumni" target="_blank" class="inline-block border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors duration-200">
                        Join us!
                    </a>
                </div>
            </div>

            <!-- Alumni Details Card -->
            @if($alumni)
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg mb-6">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Alumni Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Alumni ID -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-4 0v2m0 0h4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Alumni ID</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $alumni->AlumniID ?? 'Not Assigned' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Student Number -->
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Student Number</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $alumni->student_number ?? 'Not Available' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Full Name -->
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Full Name</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $alumni->Fullname ?? $user->name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Course -->
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Course</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $alumni->Course ?? 'Not Specified' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Batch/Graduation Year -->
                        <div class="bg-red-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0l-2 9a2 2 0 002 2h8a2 2 0 002-2l-2-9m-6 0V7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Graduation Year</p>
                                    <p class="text-lg font-semibold text-gray-900">Class of {{ $alumni->Batch ?? 'Unknown' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="bg-indigo-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 00-2 2H8a2 2 0 00-2-2V6m8 0h2a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h2"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500">Year/Section</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $alumni->Section ?? 'Not Specified' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Alumni Details Row -->
                    @if($alumni->Age || $alumni->Gender || $alumni->Occupation)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Personal & Professional Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @if($alumni->Age)
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-500">Age</p>
                                <p class="text-xl font-semibold text-gray-900">{{ $alumni->Age }} years</p>
                            </div>
                            @endif
                            @if($alumni->Gender)
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-500">Gender</p>
                                <p class="text-xl font-semibold text-gray-900">{{ $alumni->Gender }}</p>
                            </div>
                            @endif
                            @if($alumni->Occupation)
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-500">Current Occupation</p>
                                <p class="text-xl font-semibold text-gray-900">{{ $alumni->Occupation }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <!-- Social Media Links Section -->
                    @if($user->hasSocialMediaProfiles())
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Connect With Me</h3>
                        <div class="flex flex-wrap gap-3">
                            @foreach($user->getSocialMediaProfiles() as $platform => $profile)
                            <a href="{{ $profile['url'] }}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="inline-flex items-center px-4 py-2 rounded-lg text-white font-medium transition-all duration-200 hover:scale-105 hover:shadow-lg"
                               style="background-color: {{ $profile['color'] }}">
                                <i class="{{ $profile['icon'] }} mr-2"></i>
                                {{ $profile['name'] }}
                            </a>
                            @endforeach
                        </div>
                        <p class="text-sm text-gray-500 mt-3">
                            <i class="fas fa-info-circle mr-1"></i>
                            You can update your social media profiles in your 
                            <a href="{{ route('user.profile.edit') }}" class="text-blue-600 hover:text-blue-800 font-medium">profile settings</a>.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <!-- No Alumni Record Found -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-yellow-800">Alumni Record Not Found</h3>
                        <p class="text-yellow-700 mt-1">No alumni record is associated with your account. Please contact the administrator to set up your alumni profile.</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Quick Links -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold mb-3">Quick Links</h4>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('user.profile.edit') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Edit Profile
                            </a>
                            <a href="{{ route('user.announcement') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                View Announcements
                            </a>
                            <a href="{{ route('user.membership') }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                Membership
                            </a>
                        </div>
                    </div>

                    <!-- Recent Announcements -->
                    <div>
                        <h4 class="text-md font-semibold mb-3">Recent Announcements</h4>
                        @if($recentAnnouncements->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentAnnouncements as $announcement)
                                    <div class="border-l-4 border-blue-500 pl-4 py-2 bg-gray-50">
                                        <h5 class="font-medium">{{ $announcement->title }}</h5>
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit(strip_tags($announcement->content), 150) }}</p>
                                        <p class="text-xs text-gray-500 mt-2">{{ $announcement->created_at->format('M d, Y') }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('user.announcement') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View All Announcements →
                                </a>
                            </div>
                        @else
                            <p class="text-gray-500">No announcements available.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection