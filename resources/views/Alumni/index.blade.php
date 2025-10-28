<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Alumni') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Sticky Header with Controls -->
            <div class="sticky top-0 z-30 bg-white/80 backdrop-blur-sm border-b border-gray-200 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <!-- Left Controls -->
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('Alumni.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Record
                            </a>
                            
                            <button id="viewModeBtn" onclick="toggleViewMode()" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Show Details
                            </button>

                            <!-- Notification button removed - notifications are handled in the top navigation bar -->

                            <!-- Column Toggles -->
                            <div class="flex flex-wrap gap-2">
                                <button class="toggle-btn px-3 py-1 text-xs font-medium rounded-full border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 active" onclick="toggleColumnGroup('academic')">
                                    Academic
                                </button>
                                <button class="toggle-btn px-3 py-1 text-xs font-medium rounded-full border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 active" onclick="toggleColumnGroup('contact')">
                                    Contact
                                </button>
                                <button class="toggle-btn px-3 py-1 text-xs font-medium rounded-full border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 active" onclick="toggleColumnGroup('professional')">
                                    Professional
                                </button>
                            </div>
                        </div>

                        <!-- Right Controls -->
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Search alumni records..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <select id="courseFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">All Courses</option>
                            </select>
                            
                            <select id="batchFilter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">All Batches</option>
                            </select>

 
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session()->get('success') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Content -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-hidden">
                    <!-- Desktop Table View -->
                    <div class="table-container overflow-x-auto">
                        <div class="max-h-96 overflow-y-auto">
                            <table id="alumniTable" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="col-primary px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                                        <th class="col-id px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumni ID</th>
                                        <th class="col-demographic px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student #</th>
                                        <th class="col-demographic px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                        <th class="col-demographic px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                                        <th class="col-academic col-course px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                        <th class="col-academic px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year/Section</th>
                                        <th class="col-academic px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch</th>
                                        <th class="col-contact px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                        <th class="col-contact px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                        <th class="col-contact px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="col-professional px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Occupation</th>
                                        <th class="col-professional px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                        <th class="col-social px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Social Media</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($Alumni as $alumnis)
                                    <tr class="hover:bg-gray-50">
                                        <td class="col-primary px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $alumnis->Fullname }}</td>
                                        <td class="col-id px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="masked-data">***</span>
                                            <span class="real-data" style="display: none;">{{ $alumnis->AlumniID }}</span>
                                        </td>
                                        <td class="col-demographic px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="masked-data">***</span>
                                            <span class="real-data" style="display: none;">{{ $alumnis->student_number ?? 'N/A' }}</span>
                                        </td>
                                        <td class="col-demographic px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="masked-data">***</span>
                                            <span class="real-data" style="display: none;">{{ $alumnis->Age }}</span>
                                        </td>
                                        <td class="col-demographic px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="masked-data">***</span>
                                            <span class="real-data" style="display: none;">{{ $alumnis->Gender }}</span>
                                        </td>
                                        <td class="col-academic col-course px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="masked-data">***</span>
                                            <span class="real-data" style="display: none;">{{ $alumnis->Course }}</span>
                                        </td>
                                        <td class="col-academic px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="masked-data">***</span>
                                            <span class="real-data" style="display: none;">{{ $alumnis->Section }}</span>
                                        </td>
                                        <td class="col-academic px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="masked-data">***</span>
                                            <span class="real-data" style="display: none;">{{ $alumnis->Batch }}</span>
                                        </td>
                                        <td class="col-contact px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="masked-data">***</span>
                                            <span class="real-data" style="display: none;">{{ $alumnis->Contact }}</span>
                                        </td>
                                        <td class="col-contact px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="masked-data">***</span>
                                            <span class="real-data" style="display: none;">{{ $alumnis->Address }}</span>
                                        </td>
                                        <td class="col-contact px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="masked-data">***</span>
                                            <span class="real-data" style="display: none;">{{ $alumnis->Emailaddress }}</span>
                                        </td>
                                        <td class="col-professional px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="masked-data">***</span>
                                            <span class="real-data" style="display: none;">{{ $alumnis->Occupation }}</span>
                                        </td>
                                        <td class="col-professional px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="masked-data">***</span>
                                            <span class="real-data" style="display: none;">{{ $alumnis->Company }}</span>
                                        </td>
                                        <td class="col-social px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="masked-data">***</span>
                                            <span class="real-data" style="display: none;">
                                                <div class="flex space-x-2">
                                                    @if($alumnis->facebook_profile)
                                                        <a href="{{ $alumnis->facebook_profile }}" target="_blank" class="text-blue-600 hover:text-blue-800" title="Facebook">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                    @if($alumnis->linkedin_profile)
                                                        <a href="{{ $alumnis->linkedin_profile }}" target="_blank" class="text-blue-700 hover:text-blue-900" title="LinkedIn">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                    @if($alumnis->twitter_profile)
                                                        <a href="{{ $alumnis->twitter_profile }}" target="_blank" class="text-blue-400 hover:text-blue-600" title="Twitter">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                    @if($alumnis->instagram_profile)
                                                        <a href="{{ $alumnis->instagram_profile }}" target="_blank" class="text-pink-600 hover:text-pink-800" title="Instagram">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.297-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.807.875 1.297 2.026 1.297 3.323s-.49 2.448-1.297 3.323c-.875.807-2.026 1.297-3.323 1.297zm7.718-6.541c-.49 0-.875-.385-.875-.875s.385-.875.875-.875.875.385.875.875-.385.875-.875.875zm-3.323 5.666c-1.542 0-2.793-1.251-2.793-2.793s1.251-2.793 2.793-2.793 2.793 1.251 2.793 2.793-1.251 2.793-2.793 2.793z"/>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                    @if(!$alumnis->facebook_profile && !$alumnis->linkedin_profile && !$alumnis->twitter_profile && !$alumnis->instagram_profile)
                                                        <span class="text-gray-400 text-xs">No social media</span>
                                                    @endif
                                                </div>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button type="button" class="view-btn inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200" onclick="viewSingleRecord({{ $alumnis->id }})">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    View
                                                </button>
                                                <a href="{{ route('Alumni.edit', ['id' => $alumnis->id]) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="card-container hidden">
                        @foreach ($Alumni as $alumnis)
                        <div class="alumni-card bg-white border border-gray-200 rounded-lg p-6 mb-4 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $alumnis->Fullname }}</h3>
                                    <p class="text-sm text-gray-500">
                                        <span class="masked-data">*** • ***</span>
                                        <span class="real-data" style="display: none;">{{ $alumnis->Course }} • {{ $alumnis->Batch }}</span>
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <button type="button" class="view-btn inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200" onclick="viewSingleRecord({{ $alumnis->id }})">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </button>
                                    <a href="{{ route('Alumni.edit', ['id' => $alumnis->id]) }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Alumni ID</span>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <span class="masked-data">***</span>
                                        <span class="real-data" style="display: none;">{{ $alumnis->AlumniID }}</span>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Student Number</span>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <span class="masked-data">***</span>
                                        <span class="real-data" style="display: none;">{{ $alumnis->student_number ?? 'N/A' }}</span>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Age & Gender</span>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <span class="masked-data">***</span>
                                        <span class="real-data" style="display: none;">{{ $alumnis->Age }} • {{ $alumnis->Gender }}</span>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Year/Section</span>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <span class="masked-data">***</span>
                                        <span class="real-data" style="display: none;">{{ $alumnis->Section }}</span>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</span>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <span class="masked-data">***</span>
                                        <span class="real-data" style="display: none;">{{ $alumnis->Contact }}</span>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</span>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <span class="masked-data">***</span>
                                        <span class="real-data" style="display: none;">{{ $alumnis->Emailaddress }}</span>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Occupation</span>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <span class="masked-data">***</span>
                                        <span class="real-data" style="display: none;">{{ $alumnis->Occupation }}</span>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Company</span>
                                    <p class="mt-1 text-sm text-gray-900">
                                        <span class="masked-data">***</span>
                                        <span class="real-data" style="display: none;">{{ $alumnis->Company }}</span>
                                    </p>
                                </div>
                                <div>
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Social Media</span>
                                    <div class="mt-1">
                                        <span class="masked-data text-sm text-gray-900">***</span>
                                        <div class="real-data flex space-x-2" style="display: none;">
                                            @if($alumnis->facebook_profile)
                                                <a href="{{ $alumnis->facebook_profile }}" target="_blank" class="text-blue-600 hover:text-blue-800" title="Facebook">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($alumnis->linkedin_profile)
                                                <a href="{{ $alumnis->linkedin_profile }}" target="_blank" class="text-blue-700 hover:text-blue-900" title="LinkedIn">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($alumnis->twitter_profile)
                                                <a href="{{ $alumnis->twitter_profile }}" target="_blank" class="text-blue-400 hover:text-blue-600" title="Twitter">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if($alumnis->instagram_profile)
                                                <a href="{{ $alumnis->instagram_profile }}" target="_blank" class="text-pink-600 hover:text-pink-800" title="Instagram">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.297-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.807.875 1.297 2.026 1.297 3.323s-.49 2.448-1.297 3.323c-.875.807-2.026 1.297-3.323 1.297zm7.718-6.541c-.49 0-.875-.385-.875-.875s.385-.875.875-.875.875.385.875.875-.385.875-.875.875zm-3.323 5.666c-1.542 0-2.793-1.251-2.793-2.793s1.251-2.793 2.793-2.793 2.793 1.251 2.793 2.793-1.251 2.793-2.793 2.793z"/>
                                                    </svg>
                                                </a>
                                            @endif
                                            @if(!$alumnis->facebook_profile && !$alumnis->linkedin_profile && !$alumnis->twitter_profile && !$alumnis->instagram_profile)
                                                <span class="text-gray-400 text-xs">No social media</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                            <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium">{{ $Alumni->count() }}</span> of <span class="font-medium">{{ $Alumni->count() }}</span> records
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Previous</span>
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                    <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        1
                                    </a>
                                    <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <span class="sr-only">Next</span>
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Modal -->
    <div id="passwordModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center" style="display: none;">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all max-w-lg w-full mx-4">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Authentication Required</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 font-weight-500 color-#1f2937">Please enter your password to view IP addresses:</p>
                            <input type="password" id="modalPassword" placeholder="Enter your password" class="mt-3 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <div id="passwordError" class="mt-2 text-sm text-red-600" style="display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="verifyPassword()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Unlock
                </button>
                <button type="button" onclick="closePasswordModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>



    <style>
        /* Column visibility classes */
        .hide-academic .col-academic { display: none !important; }
        .hide-contact .col-contact { display: none !important; }
        .hide-professional .col-professional { display: none !important; }
        .hide-demographic .col-demographic { display: none !important; }
        
        /* Toggle button active state */
        .toggle-btn.active {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .table-container {
                display: none;
            }
            .card-container {
                display: block !important;
            }
        }
    </style>

    <script>
        // Enhanced search functionality
        document.getElementById("searchInput").addEventListener("keyup", function () {
            let filter = this.value.toLowerCase().trim().split(/\s+/);
            let rows = document.querySelectorAll("#alumniTable tbody tr");
            let cards = document.querySelectorAll(".alumni-card");

            // Filter table rows
            rows.forEach(row => {
                let columns = Array.from(row.getElementsByTagName("td"));
                let rowText = columns.map(td => td.textContent.toLowerCase()).join(" ");
                let match = filter.every(keyword => rowText.includes(keyword));
                row.style.display = match ? "" : "none";
            });

            // Filter cards
            cards.forEach(card => {
                let cardText = card.textContent.toLowerCase();
                let match = filter.every(keyword => cardText.includes(keyword));
                card.style.display = match ? "" : "none";
            });
        });

        // Column toggle functionality
        function toggleColumnGroup(group) {
            const button = event.target;
            const isActive = button.classList.contains('active');
            
            if (isActive) {
                button.classList.remove('active');
                document.body.classList.add(`hide-${group}`);
            } else {
                button.classList.add('active');
                document.body.classList.remove(`hide-${group}`);
            }
        }

        // Enhanced filter functionality
        document.getElementById('courseFilter').addEventListener('change', function() {
            filterTable();
        });

        document.getElementById('batchFilter').addEventListener('change', function() {
            filterTable();
        });

        function filterTable() {
            const courseFilter = document.getElementById('courseFilter').value.toLowerCase();
            const batchFilter = document.getElementById('batchFilter').value.toLowerCase();
            const rows = document.querySelectorAll('#alumniTable tbody tr');
            const cards = document.querySelectorAll('.alumni-card');

            rows.forEach(row => {
                const courseCell = row.cells[5].textContent.toLowerCase();
                const batchCell = row.cells[7].textContent.toLowerCase();
                
                const courseMatch = !courseFilter || courseCell.includes(courseFilter);
                const batchMatch = !batchFilter || batchCell.includes(batchFilter);
                
                row.style.display = (courseMatch && batchMatch) ? '' : 'none';
            });

            cards.forEach(card => {
                const cardText = card.textContent.toLowerCase();
                const courseMatch = !courseFilter || cardText.includes(courseFilter);
                const batchMatch = !batchFilter || cardText.includes(batchFilter);
                
                card.style.display = (courseMatch && batchMatch) ? '' : 'none';
            });
        }

        // Populate filter options
        function populateFilters() {
            const courses = new Set();
            const batches = new Set();
            
            @foreach ($Alumni as $alumni)
                courses.add('{{ $alumni->Course }}');
                batches.add('{{ $alumni->Batch }}');
            @endforeach
            
            const courseSelect = document.getElementById('courseFilter');
            const batchSelect = document.getElementById('batchFilter');
            
            courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course;
                option.textContent = course;
                courseSelect.appendChild(option);
            });
            
            batches.forEach(batch => {
                const option = document.createElement('option');
                option.value = batch;
                option.textContent = batch;
                batchSelect.appendChild(option);
            });
        }


        // Existing functionality (keeping all the original JavaScript)
        let isDetailView = false;
        let currentRecordId = null;
        let viewMode = 'all';
        let recordStates = {};

        function toggleViewMode() {
            if (isDetailView) {
                const maskedElements = document.querySelectorAll('.masked-data');
                const realElements = document.querySelectorAll('.real-data');
                const viewModeBtn = document.getElementById('viewModeBtn');
                const allViewBtns = document.querySelectorAll('.view-btn');
                
                maskedElements.forEach(el => el.style.display = '');
                realElements.forEach(el => el.style.display = 'none');
                viewModeBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>Show Details';
                
                allViewBtns.forEach(btn => {
                    btn.innerHTML = 'View';
                    const onclick = btn.getAttribute('onclick');
                    if (onclick) {
                        const recordId = onclick.match(/viewSingleRecord\((\d+)\)/)?.[1];
                        if (recordId) {
                            recordStates[recordId] = { isViewing: false };
                        }
                    }
                });
                
                isDetailView = false;
            } else {
                viewMode = 'all';
                currentRecordId = null;
                document.getElementById('passwordModal').style.display = 'flex';
                document.getElementById('modalPassword').focus();
            }
        }

        function viewSingleRecord(recordId) {
            if (recordStates[recordId] && recordStates[recordId].isViewing) {
                hideRecord(recordId);
            } else {
                viewMode = 'single';
                currentRecordId = recordId;
                document.getElementById('passwordModal').style.display = 'flex';
                document.getElementById('modalPassword').focus();
            }
        }

        function hideRecord(recordId) {
            const allRows = document.querySelectorAll('#alumniTable tbody tr');
            const allCards = document.querySelectorAll('.alumni-card');
            
            // Hide in table
            allRows.forEach(row => {
                const viewBtn = row.querySelector('.view-btn');
                if (viewBtn && viewBtn.getAttribute('onclick').includes(recordId)) {
                    const maskedElements = row.querySelectorAll('.masked-data');
                    const realElements = row.querySelectorAll('.real-data');
                    
                    maskedElements.forEach(el => el.style.display = '');
                    realElements.forEach(el => el.style.display = 'none');
                    viewBtn.innerHTML = 'View';
                }
            });
            
            // Hide in cards
            allCards.forEach(card => {
                const viewBtn = card.querySelector('.view-btn');
                if (viewBtn && viewBtn.getAttribute('onclick').includes(recordId)) {
                    const maskedElements = card.querySelectorAll('.masked-data');
                    const realElements = card.querySelectorAll('.real-data');
                    
                    maskedElements.forEach(el => el.style.display = '');
                    realElements.forEach(el => el.style.display = 'none');
                    viewBtn.innerHTML = 'View';
                }
            });
            
            recordStates[recordId] = { isViewing: false };
        }

        function verifyPassword() {
            const password = document.getElementById('modalPassword').value;
            const errorDiv = document.getElementById('passwordError');
            
            fetch('/verify-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ password: password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (viewMode === 'all') {
                        showAllDetails();
                    } else if (viewMode === 'single' && currentRecordId) {
                        showSingleRecord(currentRecordId);
                    }
                    closePasswordModal();
                } else {
                    errorDiv.textContent = 'Incorrect password. Please try again.';
                    errorDiv.style.display = 'block';
                    document.getElementById('modalPassword').value = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.style.display = 'block';
            });
        }

        function showAllDetails() {
            const maskedElements = document.querySelectorAll('.masked-data');
            const realElements = document.querySelectorAll('.real-data');
            const viewModeBtn = document.getElementById('viewModeBtn');
            
            maskedElements.forEach(el => el.style.display = 'none');
            realElements.forEach(el => el.style.display = '');
            viewModeBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path></svg>Hide Details';
            
            isDetailView = true;
        }

        function showSingleRecord(recordId) {
            const allRows = document.querySelectorAll('#alumniTable tbody tr');
            const allCards = document.querySelectorAll('.alumni-card');
            
            // Show in table
            allRows.forEach(row => {
                const viewBtn = row.querySelector('.view-btn');
                if (viewBtn && viewBtn.getAttribute('onclick').includes(recordId)) {
                    const maskedElements = row.querySelectorAll('.masked-data');
                    const realElements = row.querySelectorAll('.real-data');
                    
                    maskedElements.forEach(el => el.style.display = 'none');
                    realElements.forEach(el => el.style.display = '');
                    viewBtn.innerHTML = 'Hide';
                }
            });
            
            // Show in cards
            allCards.forEach(card => {
                const viewBtn = card.querySelector('.view-btn');
                if (viewBtn && viewBtn.getAttribute('onclick').includes(recordId)) {
                    const maskedElements = card.querySelectorAll('.masked-data');
                    const realElements = card.querySelectorAll('.real-data');
                    
                    maskedElements.forEach(el => el.style.display = 'none');
                    realElements.forEach(el => el.style.display = '');
                    viewBtn.innerHTML = 'Hide';
                }
            });
            
            recordStates[recordId] = { isViewing: true };
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
            document.getElementById('modalPassword').value = '';
            document.getElementById('passwordError').style.display = 'none';
        }

        // Handle Enter key in password modal
        document.getElementById('modalPassword').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                verifyPassword();
            }
        });

        // Notification functions removed - notifications are handled in the top navigation bar

        // Auto-hide success message
        setTimeout(() => {
            let successMessage = document.querySelector(".bg-green-50");
            if (successMessage) {
                successMessage.style.transition = "opacity 0.5s";
                successMessage.style.opacity = "0";
                setTimeout(() => successMessage.remove(), 500);
            }
        }, 3000);

        // Initialize filters on page load
        document.addEventListener('DOMContentLoaded', function() {
            populateFilters();
        });
    </script>
</x-admin-layout>