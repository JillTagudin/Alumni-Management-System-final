@php
    // Role protection at view level
    if (!Auth::user()->isHR()) {
        abort(403, 'Unauthorized access. HR role required.');
    }
@endphp

<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Announcement') }}
        </h2>
    </x-slot>

    <!-- Quill.js CDN -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 success-message">
                            {{ session('success') }}
                        </div>
                        <script>
                            // Auto-hide success message after 3 seconds
                            setTimeout(() => {
                                let successMessage = document.querySelector(".success-message");
                                if (successMessage) {
                                    successMessage.style.transition = "opacity 0.5s";
                                    successMessage.style.opacity = "0";
                                    setTimeout(() => successMessage.remove(), 500);
                                }
                            }, 3000);
                        </script>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Create New Announcement</h3>
                        <p class="text-sm text-gray-600 mt-1">Your announcement will be submitted for admin approval before being published.</p>
                    </div>

                    <form method="POST" action="{{ route('admin.announcement.store') }}" class="space-y-6" enctype="multipart/form-data">
                        @csrf
                        
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Announcement Title</label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   value="{{ old('title') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="category" 
                                    id="category" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required>
                                <option value="">Select a category...</option>
                                <option value="general" {{ old('category') == 'general' ? 'selected' : '' }}>General</option>
                                <option value="important" {{ old('category') == 'important' ? 'selected' : '' }}>Important</option>
                                <option value="event" {{ old('category') == 'event' ? 'selected' : '' }}>Event</option>
                                <option value="notice" {{ old('category') == 'notice' ? 'selected' : '' }}>Notice</option>
                                <option value="job_offers" {{ old('category') == 'job_offers' ? 'selected' : '' }}>Job Offers</option>
                                <option value="scholarship" {{ old('category') == 'scholarship' ? 'selected' : '' }}>Scholarship</option>
                            </select>
                            @error('category')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">Announcement Content</label>
                            <div id="editor" style="height: 400px;"></div>
                            <textarea name="content" id="content" style="display: none;" required>{{ old('content') }}</textarea>
                            @error('content')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="attachments" class="block text-sm font-medium text-gray-700">File Attachments</label>
                            <input type="file" 
                                   name="attachments[]" 
                                   id="attachments" 
                                   multiple 
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.mp3,.wav,.ogg,.mp4,.avi,.mov" 
                                   class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-sm text-gray-500">Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG, GIF, MP3, WAV, OGG, MP4, AVI, MOV (Max 50MB each)</p>
                            @error('attachments.*')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        <strong>Note:</strong> As an HR representative, your announcement will be submitted for admin approval before being published to alumni.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('hr.dashboard') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Submit for Approval
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quill.js Initialization -->
    <script>
        var quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Write your announcement content here...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['blockquote', 'code-block'],
                    ['link', 'image'],
                    ['clean']
                ]
            }
        });

        // Set initial content if there's old input
        @if(old('content'))
            quill.root.innerHTML = {!! json_encode(old('content')) !!};
        @endif

        // Update hidden textarea when content changes
        quill.on('text-change', function() {
            document.getElementById('content').value = quill.root.innerHTML;
        });

        // Handle form submission
        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('content').value = quill.root.innerHTML;
        });
    </script>
</x-admin-layout>