@extends('layouts.user')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Job Opportunities') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Search and Filters Section (similar to ActivityLogController) -->
            <div class="bg-white bg-opacity-80 backdrop-blur-sm shadow rounded-lg mb-4">
                <div class="px-6 py-4">
                    @if(request('global_search'))
                        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800">Search Results</h4>
                                    <p class="text-sm text-blue-600">Showing results for: "<strong>{{ request('global_search') }}</strong>"</p>
                                </div>
                                <a href="{{ route('user.job-opportunity') }}" class="text-blue-600 hover:text-blue-800 text-sm underline">Clear Search</a>
                            </div>
                        </div>
                    @endif
                    
                    <div class="flex flex-wrap gap-4 items-center">
                        <!-- Search Input -->
                        <div class="flex-1 min-w-64">
                            <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <div class="flex gap-2">
                                <input type="text" id="searchInput" 
                                       placeholder="Search job opportunities..." 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ request('global_search') }}">
                                <button type="button" 
                                        onclick="searchAllRecords()" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200 whitespace-nowrap"
                                        title="Search across all job opportunities">
                                    Search All
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filters Form -->
                    <form method="GET" action="{{ route('user.job-opportunity') }}" class="mt-4" id="filtersForm">
                        <input type="hidden" name="global_search" id="globalSearchInput" value="{{ request('global_search') }}">
                        <div class="flex flex-wrap gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Job Type</label>
                                <select name="job_type_filter" class="pl-3 pr-8 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white" onchange="this.form.submit()">
                                    <option value="">All Types</option>
                                    @foreach($jobTypes as $type)
                                        <option value="{{ $type }}" {{ request('job_type_filter') == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                <select name="location_filter" class="pl-3 pr-8 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white" onchange="this.form.submit()">
                                    <option value="">All Locations</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location }}" {{ request('location_filter') == $location ? 'selected' : '' }}>
                                            {{ $location }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                                <select name="company_filter" class="pl-3 pr-8 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white" onchange="this.form.submit()">
                                    <option value="">All Companies</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company }}" {{ request('company_filter') == $company ? 'selected' : '' }}>
                                            {{ $company }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                                <input type="date" name="date_from" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" value="{{ request('date_from') }}" onchange="this.form.submit()">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                                <input type="date" name="date_to" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" value="{{ request('date_to') }}" onchange="this.form.submit()">
                            </div>
                            
                            @if(request()->hasAny(['job_type_filter', 'location_filter', 'company_filter', 'date_from', 'date_to', 'global_search']))
                                <div>
                                    <a href="{{ route('user.job-opportunity') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-200">Clear All Filters</a>
                                </div>
                            @endif
                        </div>
                    </form>
                    
                    <!-- Active Filters Display -->
                    @if(request()->hasAny(['global_search', 'job_type_filter', 'location_filter', 'company_filter', 'date_from', 'date_to']))
                        <div class="mt-4 p-3 bg-gray-50 rounded-md">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Active Filters:</h4>
                            <div class="flex flex-wrap gap-2">
                                @if(request('global_search'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Search: {{ request('global_search') }}
                                        <a href="{{ request()->fullUrlWithQuery(['global_search' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                                    </span>
                                @endif
                                @if(request('job_type_filter'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Type: {{ request('job_type_filter') }}
                                        <a href="{{ request()->fullUrlWithQuery(['job_type_filter' => null]) }}" class="ml-1 text-green-600 hover:text-green-800">×</a>
                                    </span>
                                @endif
                                @if(request('location_filter'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Location: {{ request('location_filter') }}
                                        <a href="{{ request()->fullUrlWithQuery(['location_filter' => null]) }}" class="ml-1 text-yellow-600 hover:text-yellow-800">×</a>
                                    </span>
                                @endif
                                @if(request('company_filter'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Company: {{ request('company_filter') }}
                                        <a href="{{ request()->fullUrlWithQuery(['company_filter' => null]) }}" class="ml-1 text-purple-600 hover:text-purple-800">×</a>
                                    </span>
                                @endif
                                @if(request('date_from'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        From: {{ request('date_from') }}
                                        <a href="{{ request()->fullUrlWithQuery(['date_from' => null]) }}" class="ml-1 text-indigo-600 hover:text-indigo-800">×</a>
                                    </span>
                                @endif
                                @if(request('date_to'))
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                        To: {{ request('date_to') }}
                                        <a href="{{ request()->fullUrlWithQuery(['date_to' => null]) }}" class="ml-1 text-pink-600 hover:text-pink-800">×</a>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            @if($jobOpportunities->count() > 0)
                <!-- Job Opportunities List -->
                <div class="max-w-4xl mx-auto space-y-6">
                    @foreach($jobOpportunities as $job)
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6 hover:shadow-lg transition-shadow duration-300">
                            <!-- Job Header -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $job->title }}</h3>
                                    <div class="flex items-center text-gray-600 mb-2">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="font-medium">{{ $job->company }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-600 mb-3">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>{{ $job->location }}</span>
                                    </div>
                                </div>
                                
                                <!-- Job Type Badge -->
                                <div class="ml-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $job->job_type_badge_color }}">
                                        {{ ucfirst(str_replace('-', ' ', $job->job_type)) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Job Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                @if($job->salary_range)
                                    <div class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        <span class="font-medium">Salary:</span>
                                        <span class="ml-1">{{ $job->salary_range }}</span>
                                    </div>
                                @endif
                                
                                @if($job->application_deadline)
                                    <div class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-medium">Deadline:</span>
                                        <span class="ml-1">{{ $job->application_deadline->format('M d, Y') }}</span>
                                    </div>
                                @endif
                                
                                @if($job->contact_number)
                                    <div class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        <span class="font-medium">Contact:</span>
                                        <span class="ml-1">{{ $job->contact_number }}</span>
                                    </div>
                                @endif
                                
                                @if($job->contact_email)
                                    <div class="flex items-center text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-medium">Email:</span>
                                        <span class="ml-1">{{ $job->contact_email }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Job Description Preview -->
                            <div class="mb-4">
                                <div class="text-gray-700 line-clamp-3">
                                    {!! Str::limit(strip_tags($job->description), 200) !!}
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                                @if($job->application_url)
                                    <a href="{{ $job->application_url }}" target="_blank" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        Apply Now
                                    </a>
                                @endif
                                
                                <button onclick="toggleJobDetails({{ $job->id }})" 
                                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    View Details
                                </button>
                            </div>

                            <!-- Expandable Job Details -->
                            <div id="job-details-{{ $job->id }}" class="hidden mt-6 pt-6 border-t border-gray-200">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Description -->
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-3">Job Description</h4>
                                        <div class="text-gray-700 prose prose-sm max-w-none">
                                            {!! $job->description !!}
                                        </div>
                                    </div>
                                    
                                    <!-- Requirements -->
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 mb-3">Requirements</h4>
                                        <div class="text-gray-700 prose prose-sm max-w-none">
                                            {!! $job->requirements !!}
                                        </div>
                                    </div>
                                </div>

                                <!-- Attachments -->
                                @if($job->attachments && count($job->attachments) > 0)
                                    <div class="mt-6">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-3">Attachments</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($job->attachments as $attachment)
                                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank" 
                                                   class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-md transition-colors duration-200">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                    </svg>
                                                    {{ $attachment['name'] }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Posted Date -->
                                <div class="mt-6 text-sm text-gray-500">
                                    Posted on {{ $job->created_at->format('M d, Y') }} by {{ $job->user->name }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- No Job Opportunities -->
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2m-8 0V6a2 2 0 00-2 2v6.001"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Job Opportunities Available</h3>
                    <p class="text-gray-600">Check back later for new job postings from our partners and alumni network.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Global search function (similar to ActivityLogController)
        function searchAllRecords() {
            const searchTerm = document.getElementById('searchInput').value.trim();
            
            if (!searchTerm) {
                alert('Please enter a search term');
                return;
            }
            
            // Set the global search input value
            document.getElementById('globalSearchInput').value = searchTerm;
            
            // Submit the form to perform global search
            document.getElementById('filtersForm').submit();
        }

        // Toggle job details
        function toggleJobDetails(jobId) {
            const detailsDiv = document.getElementById('job-details-' + jobId);
            const button = event.target.closest('button');
            
            if (detailsDiv.classList.contains('hidden')) {
                detailsDiv.classList.remove('hidden');
                button.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                    Hide Details
                `;
            } else {
                detailsDiv.classList.add('hidden');
                button.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    View Details
                `;
            }
        }

        // Allow Enter key to trigger search
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && event.target.id === 'searchInput') {
                event.preventDefault();
                searchAllRecords();
            }
        });

        // Real-time search as user types (optional enhancement)
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = this.value.trim();
                if (searchTerm.length >= 3) {
                    // Perform search after 3 characters
                    document.getElementById('globalSearchInput').value = searchTerm;
                    document.getElementById('filtersForm').submit();
                }
            }, 500); // Wait 500ms after user stops typing
        });
    </script>

    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .prose ul, .prose ol {
            padding-left: 1.5rem;
        }
        
        .prose ul {
            list-style-type: disc;
        }
        
        .prose ol {
            list-style-type: decimal;
        }
        
        .prose li {
            margin: 0.25rem 0;
        }
    </style>
@endsection