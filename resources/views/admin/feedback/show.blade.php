<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Feedback Details') }}
        </h2>
    </x-slot>

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
                <a href="{{ route('admin.feedback.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center transition duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to All Feedback
                </a>
            </div>

            <!-- Feedback Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $feedback->subject }}</h3>
                            <p class="text-sm text-gray-600">Submitted by {{ $feedback->user->name }} on {{ $feedback->created_at->format('F d, Y \\a\\t g:i A') }}</p>
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

                    <!-- User Information -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Submitted By</label>
                            <p class="text-sm text-gray-900">{{ $feedback->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $feedback->user->email }}</p>
                        </div>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">User Message</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-900 whitespace-pre-wrap">{{ $feedback->message }}</p>
                        </div>
                    </div>

                    <!-- Admin Response Section -->
                    @if($feedback->admin_response)
                        <div class="border-t pt-6 mb-6">
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
                    @endif

                    <!-- Admin Actions -->
                    @if($feedback->status !== 'resolved')
                        <div class="border-t pt-6 mb-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Admin Actions</h4>
                            
                            <!-- Status Update Form -->
                            <form method="POST" action="{{ route('admin.feedback.update', $feedback) }}" class="mb-4">
                                @csrf
                                @method('PATCH')
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700">Update Status</label>
                                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="pending" {{ $feedback->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="in_progress" {{ $feedback->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="resolved" {{ $feedback->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                        </select>
                                    </div>
                                    <div class="pt-6">
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            Update Status
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Response Form -->
                            <form method="POST" action="{{ route('admin.feedback.respond', $feedback) }}">
                                @csrf
                                <div class="mb-4">
                                    <label for="response" class="block text-sm font-medium text-gray-700 mb-2">Admin Response</label>
                                    <textarea name="admin_response" id="admin_response" rows="4" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                        placeholder="Enter your response to the user...">{{ old('admin_response', $feedback->admin_response) }}</textarea>
                                    @error('response')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="flex items-center space-x-4">
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Send Response
                                    </button>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="mark_resolved" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-600">Mark as resolved</span>
                                    </label>
                                </div>
                            </form>
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
                                        <p class="text-sm text-gray-600">Feedback is being reviewed by admin team</p>
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

                    <!-- Additional Actions -->
                    <div class="border-t pt-6 mt-6">
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-3">
                                <a href="{{ route('admin.feedback.index') }}" class="text-blue-600 hover:text-blue-900">
                                    View All Feedback
                                </a>
                                <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-900">
                                    View Reports
                                </a>
                            </div>
                            <div class="flex space-x-3">
                                @if($feedback->status === 'resolved')
                                    <span class="text-sm text-green-600 font-medium">
                                        ✓ This feedback has been resolved
                                    </span>
                                @else
                                    <span class="text-sm text-yellow-600">
                                        Awaiting admin action
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>