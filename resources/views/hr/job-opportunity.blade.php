@php
    // Role protection at view level
    if (!Auth::user()->isHR()) {
        abort(403, 'Unauthorized access. HR role required.');
    }
@endphp

<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('HR Job Opportunity Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Job Opportunity Creation Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Post New Job Opportunity</h3>
                    <p class="text-sm text-gray-600 mb-4">Job opportunities posted by HR require admin approval before being published.</p>
                    
                    <form method="POST" action="{{ route('job-opportunity.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Job Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Job Title *</label>
                                <input type="text" name="title" id="title" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('title') }}" placeholder="e.g., Software Developer">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Company -->
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company *</label>
                                <input type="text" name="company" id="company" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('company') }}" placeholder="e.g., Tech Solutions Inc.">
                                @error('company')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location *</label>
                                <input type="text" name="location" id="location" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('location') }}" placeholder="e.g., Manila, Philippines">
                                @error('location')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Job Type -->
                            <div>
                                <label for="job_type" class="block text-sm font-medium text-gray-700 mb-2">Job Type *</label>
                                <select name="job_type" id="job_type" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select Job Type</option>
                                    <option value="full-time" {{ old('job_type') == 'full-time' ? 'selected' : '' }}>Full-time</option>
                                    <option value="part-time" {{ old('job_type') == 'part-time' ? 'selected' : '' }}>Part-time</option>
                                    <option value="contract" {{ old('job_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                                    <option value="internship" {{ old('job_type') == 'internship' ? 'selected' : '' }}>Internship</option>
                                    <option value="remote" {{ old('job_type') == 'remote' ? 'selected' : '' }}>Remote</option>
                                </select>
                                @error('job_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Salary Range -->
                            <div>
                                <label for="salary_range" class="block text-sm font-medium text-gray-700 mb-2">Salary Range</label>
                                <input type="text" name="salary_range" id="salary_range" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('salary_range') }}" placeholder="e.g., ₱25,000 - ₱35,000">
                                @error('salary_range')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Application Deadline -->
                            <div>
                                <label for="application_deadline" class="block text-sm font-medium text-gray-700 mb-2">Application Deadline</label>
                                <input type="date" name="application_deadline" id="application_deadline" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('application_deadline') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                @error('application_deadline')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Job Description -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Job Description *</label>
                            <div id="description-editor" style="height: 200px;"></div>
                            <textarea name="description" id="description-content" class="hidden">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Requirements -->
                        <div class="mb-6">
                            <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">Requirements *</label>
                            <div id="requirements-editor" style="height: 200px;"></div>
                            <textarea name="requirements" id="requirements-content" class="hidden">{{ old('requirements') }}</textarea>
                            @error('requirements')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <!-- Application URL -->
                            <div>
                                <label for="application_url" class="block text-sm font-medium text-gray-700 mb-2">Application URL</label>
                                <input type="url" name="application_url" id="application_url" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('application_url') }}" placeholder="https://company.com/apply">
                                @error('application_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contact Email -->
                            <div>
                                <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                                <input type="email" name="contact_email" id="contact_email" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('contact_email') }}" placeholder="hr@company.com">
                                @error('contact_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contact Number -->
                            <div>
                                <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
                                <input type="tel" name="contact_number" id="contact_number" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('contact_number') }}" placeholder="+1 (555) 123-4567">
                                @error('contact_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- File Attachments -->
                        <div class="mb-6">
                            <label for="attachments" class="block text-sm font-medium text-gray-700 mb-2">Attachments (Optional)</label>
                            <input type="file" name="attachments[]" id="attachments" multiple 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <p class="mt-1 text-sm text-gray-500">Supported formats: PDF, DOC, DOCX, JPG, PNG. Max 50MB per file.</p>
                            @error('attachments.*')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Submit for Approval
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Quill.js for rich text editing -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <script>
        // Initialize Quill editors
        var descriptionQuill = new Quill('#description-editor', {
            theme: 'snow',
            placeholder: 'Enter job description...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        var requirementsQuill = new Quill('#requirements-editor', {
            theme: 'snow',
            placeholder: 'Enter job requirements...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link'],
                    ['clean']
                ]
            }
        });

        // Update hidden textareas on content change
        descriptionQuill.on('text-change', function() {
            document.getElementById('description-content').value = descriptionQuill.root.innerHTML;
        });

        requirementsQuill.on('text-change', function() {
            document.getElementById('requirements-content').value = requirementsQuill.root.innerHTML;
        });

        // Set initial content if available
        @if(old('description'))
            descriptionQuill.root.innerHTML = {!! json_encode(old('description')) !!};
        @endif

        @if(old('requirements'))
            requirementsQuill.root.innerHTML = {!! json_encode(old('requirements')) !!};
        @endif

        // Update textareas before form submission
        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('description-content').value = descriptionQuill.root.innerHTML;
            document.getElementById('requirements-content').value = requirementsQuill.root.innerHTML;
        });
    </script>
</x-admin-layout>