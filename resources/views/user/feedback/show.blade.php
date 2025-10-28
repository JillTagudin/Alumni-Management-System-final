@extends('layouts.user')

@section('title', $feedback->subject)

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Feedback Details') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Navigation -->
            <div class="mb-6">
                <a href="{{ route('user.feedback.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center transition duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to My Feedback
                </a>
            </div>

            <!-- Feedback Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $feedback->subject }}</h3>
                            <p class="text-sm text-gray-600">Submitted on {{ $feedback->created_at->format('F d, Y \\a\\t g:i A') }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Priority Badge -->
                            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                @if($feedback->priority === 'high') bg-red-100 text-red-800
                                @elseif($feedback->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($feedback->priority) }} Priority
                            </span>
                            <!-- Status Badge -->
                            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                @if($feedback->status === 'resolved') bg-green-100 text-green-800
                                @elseif($feedback->status === 'in_progress') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $feedback->status)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Feedback Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <p class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $feedback->category)) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Feedback ID</label>
                            <p class="text-sm text-gray-900">#{{ $feedback->id }}</p>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Message</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $feedback->message }}</p>
                        </div>
                    </div>

                    <!-- Response Section -->
                    @if($feedback->admin_response)
                        <div class="border-t pt-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Admin Response</h4>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-blue-900">
                                        Response from {{ $feedback->respondedBy ? $feedback->respondedBy->fullname : 'Admin' }}
                                    </span>
                                    <span class="text-sm text-blue-700">
                                        {{ $feedback->responded_at ? $feedback->responded_at->format('M d, Y \\a\\t g:i A') : 'Recently' }}
                                    </span>
                                </div>
                                <p class="text-blue-900 whitespace-pre-wrap">{{ $feedback->admin_response }}</p>
                            </div>
                        </div>
                    @else
                        <div class="border-t pt-6">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-yellow-800">Awaiting Response</h4>
                                        <p class="text-sm text-yellow-700 mt-1">
                                            Your feedback has been received and is being reviewed. Please be patient as it may take some time before you may receive a response.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Timeline -->
                    <div class="border-t pt-6 mt-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">Timeline</h4>
                        <div class="space-y-4">
                            <!-- Submitted -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">Feedback Submitted</p>
                                    <p class="text-sm text-gray-600">{{ $feedback->created_at->format('F d, Y \\a\\t g:i A') }}</p>
                                </div>
                            </div>

                            @if($feedback->status === 'in_progress')
                                <!-- In Progress -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">Under Review</p>
                                        <p class="text-sm text-gray-600">Your feedback is being reviewed by our team</p>
                                    </div>
                                </div>
                            @endif

                            @if($feedback->status === 'resolved' && $feedback->responded_at)
                                <!-- Resolved -->
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">Resolved</p>
                                        <p class="text-sm text-gray-600">{{ $feedback->responded_at->format('F d, Y \\a\\t g:i A') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="border-t pt-6 mt-6">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('user.feedback.create') }}" class="text-blue-600 hover:text-blue-900">
                                Submit New Feedback
                            </a>
                            <div class="flex space-x-3">
                                @if($feedback->status !== 'resolved')
                                    <span class="text-sm text-gray-500">
                                        Need to add more details? Contact support directly.
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection