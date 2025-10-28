@extends('layouts.user')

@section('content')
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Feedback Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">We Value Your Feedback</h3>
                        <p class="text-gray-600">Your feedback helps us improve our services and better serve the alumni community. Please share your thoughts, suggestions, or report any issues you've encountered.</p>
                    </div>

                    <form method="POST" action="{{ route('user.feedback.store') }}" class="space-y-6">
                        @csrf

                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                                Subject <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="subject" 
                                   id="subject" 
                                   value="{{ old('subject') }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('subject') border-red-500 @enderror" 
                                   placeholder="Brief description of your feedback"
                                   required>
                            @error('subject')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select name="category" 
                                    id="category" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('category') border-red-500 @enderror" 
                                    required>
                                <option value="">Select a category</option>
                                <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>General Feedback</option>
                                <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Technical Issue</option>
                                <option value="feature_request" {{ old('category') === 'feature_request' ? 'selected' : '' }}>Feature Request</option>
                                <option value="bug_report" {{ old('category') === 'bug_report' ? 'selected' : '' }}>Bug Report</option>
                                <option value="complaint" {{ old('category') === 'complaint' ? 'selected' : '' }}>Complaint</option>
                                <option value="suggestion" {{ old('category') === 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                                Priority <span class="text-red-500">*</span>
                            </label>
                            <select name="priority" 
                                    id="priority" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('priority') border-red-500 @enderror" 
                                    required>
                                <option value="">Select priority level</option>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low - General feedback or minor suggestions</option>
                                <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium - Important but not urgent</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High - Urgent issue requiring immediate attention</option>
                            </select>
                            @error('priority')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                                Message <span class="text-red-500">*</span>
                            </label>
                            <textarea name="message" 
                                      id="message" 
                                      rows="6" 
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('message') border-red-500 @enderror" 
                                      placeholder="Please provide detailed information about your feedback. Include steps to reproduce if reporting a bug, or specific details about your suggestion."
                                      required>{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Minimum 10 characters required. Be as specific as possible to help us understand and address your feedback.</p>
                        </div>

                        <!-- Guidelines -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-blue-900 mb-2">Feedback Guidelines</h4>
                            <ul class="text-sm text-blue-800 space-y-1">
                                <li>• Be specific and provide clear details about your feedback</li>
                                <li>• For bug reports, include steps to reproduce the issue</li>
                                <li>• For feature requests, explain how it would benefit the community</li>
                                <li>• Keep your feedback constructive and professional</li>
                                <li>• You will receive a response within 2-3 business days</li>
                            </ul>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between">
                            <a href="{{ route('user.feedback.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                View My Feedback
                            </a>
                            <div class="flex space-x-3">
                                <button type="reset" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                                    Clear Form
                                </button>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                                    Submit Feedback
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Feedback -->
            @if(isset($recentFeedback) && $recentFeedback->count() > 0)
                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Your Recent Feedback</h3>
                        <div class="space-y-3">
                            @foreach($recentFeedback as $feedback)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $feedback->subject }}</h4>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                @switch($feedback->status)
                                                    @case('resolved') bg-green-100 text-green-800 @break
                                                    @case('in_progress') bg-blue-100 text-blue-800 @break
                                                    @case('pending') bg-yellow-100 text-yellow-800 @break
                                                    @default bg-gray-100 text-gray-800
                                                @endswitch">
                                                {{ ucfirst(str_replace('_', ' ', $feedback->status)) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ Str::limit($feedback->message, 100) }}</p>
                                        <div class="flex items-center justify-between mt-2">
                                            <span class="text-xs text-gray-500">{{ $feedback->created_at->format('M d, Y') }}</span>
                                            <a href="{{ route('user.feedback.show', $feedback) }}" class="text-xs text-blue-600 hover:text-blue-900">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('user.feedback.index') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                View All My Feedback →
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Form validation and enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const messageTextarea = document.getElementById('message');
            const subjectInput = document.getElementById('subject');
            
            // Character count for message
            const charCount = document.createElement('div');
            charCount.className = 'text-sm text-gray-500 mt-1';
            messageTextarea.parentNode.appendChild(charCount);
            
            function updateCharCount() {
                const length = messageTextarea.value.length;
                charCount.textContent = `${length} characters`;
                
                if (length < 10) {
                    charCount.className = 'text-sm text-red-500 mt-1';
                } else {
                    charCount.className = 'text-sm text-gray-500 mt-1';
                }
            }
            
            messageTextarea.addEventListener('input', updateCharCount);
            updateCharCount();
            
            // Form submission confirmation
            form.addEventListener('submit', function(e) {
                const subject = subjectInput.value.trim();
                const message = messageTextarea.value.trim();
                
                if (message.length < 10) {
                    e.preventDefault();
                    alert('Please provide a more detailed message (at least 10 characters).');
                    messageTextarea.focus();
                    return;
                }
                
                if (!confirm('Are you sure you want to submit this feedback?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection