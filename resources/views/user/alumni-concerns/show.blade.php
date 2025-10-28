
@extends('layouts.user')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Header Section with Title and Back Button -->
                <div class="flex items-center mb-6">
                    <a href="{{ route('user.alumni-concerns.index') }}" 
                       class="text-blue-600 hover:text-blue-800 mr-4">
                        ← Back to Concerns
                    </a>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('Alumni Concern Details') }}
                    </h2>
                </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">{{ $concern->title }}</h2>
                        <p class="text-sm text-gray-600 mt-1">Submitted on {{ $concern->created_at->format('F d, Y \\a\\t g:i A') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $concern->status_color }}">
                            {{ $concern->status_label }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <p class="text-sm text-gray-900">{{ $concern->category_label }}</p>
                    </div>
                    {{-- Priority field hidden
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Priority</label>
                        <p class="text-sm {{ $concern->priority_color }}">{{ $concern->priority_label }}</p>
                    </div>
                    --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <p class="text-sm text-gray-900">{{ $concern->status_label }}</p>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p class="text-gray-800 whitespace-pre-wrap">{{ $concern->description }}</p>
                    </div>
                </div>

                @if($concern->admin_response)
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Response from Alumni Management</h3>
                        <div class="bg-blue-50 p-4 rounded-md">
                            <p class="text-gray-800 whitespace-pre-wrap">{{ $concern->admin_response }}</p>
                            @if($concern->responder)
                                <div class="mt-3 text-sm text-gray-600">
                                    <p>Responded by: {{ $concern->responder->name }}</p>
                                    <p>Response date: {{ $concern->responded_at->format('F d, Y \\a\\t g:i A') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="border-t pt-6">
                        <div class="bg-yellow-50 p-4 rounded-md">
                            <p class="text-yellow-800">We have received your concern and will respond as soon as possible. Thank you for your patience.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
