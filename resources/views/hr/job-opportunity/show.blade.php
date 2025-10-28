@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Job Opportunity Details') }}
    </h2>
@endsection

@section('content')
    <div class="py-8 min-h-screen" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('Images/bantay.jpg') }}'); background-size: cover; background-position: center; background-attachment: fixed;">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('hr.job-opportunities') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Job Opportunities
                </a>
            </div>

            <!-- Job Opportunity Card -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="bg-white border-b-4 border-blue-500 px-6 py-8">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold mb-2 text-gray-900">{{ $jobOpportunity->title }}</h1>
                            <p class="text-gray-700 text-lg mb-4">{{ $jobOpportunity->company }}</p>
                            <div class="flex flex-wrap items-center gap-4 text-gray-600">
                                @if($jobOpportunity->location)
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $jobOpportunity->location }}
                                    </div>
                                @endif
                                @if($jobOpportunity->job_type)
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ ucfirst(str_replace('_', ' ', $jobOpportunity->job_type)) }}
                                    </div>
                                @endif
                                @if($jobOpportunity->salary_range)
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $jobOpportunity->salary_range }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            {{ ucfirst($jobOpportunity->status) }}
                        </span>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- Job Description -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Job Description</h2>
                        <div class="prose max-w-none text-gray-700">
                            {!! $jobOpportunity->description !!}
                        </div>
                    </div>

                    <!-- Requirements -->
                    @if($jobOpportunity->requirements)
                        <div class="mb-8">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Requirements</h2>
                            <div class="prose max-w-none text-gray-700">
                                {!! $jobOpportunity->requirements !!}
                            </div>
                        </div>
                    @endif

                    <!-- Job Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-3">Job Information</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Posted Date:</span>
                                    <span class="font-medium">{{ $jobOpportunity->created_at->format('M j, Y') }}</span>
                                </div>
                                @if($jobOpportunity->application_deadline)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Application Deadline:</span>
                                        <span class="font-medium text-red-600">{{ \Carbon\Carbon::parse($jobOpportunity->application_deadline)->format('M j, Y') }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="font-medium">{{ ucfirst($jobOpportunity->status) }}</span>
                                </div>
                            </div>
                        </div>

                        @if($jobOpportunity->contact_email || $jobOpportunity->contact_phone)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-lg font-medium text-gray-900 mb-3">Contact Information</h3>
                                <div class="space-y-2">
                                    @if($jobOpportunity->contact_email)
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                            </svg>
                                            <a href="mailto:{{ $jobOpportunity->contact_email }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $jobOpportunity->contact_email }}
                                            </a>
                                        </div>
                                    @endif
                                    @if($jobOpportunity->contact_phone)
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                            </svg>
                                            <a href="tel:{{ $jobOpportunity->contact_phone }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $jobOpportunity->contact_phone }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Application Instructions -->
                    @if($jobOpportunity->how_to_apply)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-blue-900 mb-3">How to Apply</h3>
                            <div class="text-blue-800">
                                {!! $jobOpportunity->how_to_apply !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection