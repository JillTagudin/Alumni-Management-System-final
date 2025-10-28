<x-admin-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Email Composer Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Compose Email</h3>
                    
                    <form method="POST" action="{{ route('send-mail') }}">
                        @csrf
                        
                        <!-- Recipient Type -->
                        <div class="mb-6">
                            <label for="recipient_type" class="block text-sm font-medium text-gray-700 mb-2">Recipient Type</label>
                            <select name="recipient_type" id="recipient_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Select recipient type</option>
                                <option value="all">All Users</option>
                                <option value="role">By Role</option>
                                <option value="specific">Specific User</option>
                            </select>
                            @error('recipient_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role Selection (shown when recipient_type is 'role') -->
                        <div id="role_selection" class="mb-6 hidden">
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Select Role</label>
                            <select name="role" id="role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select role</option>
                                <option value="Alumni">Alumni</option>
                                <option value="Staff">Staff</option>
                                <option value="HR">HR</option>
                                <option value="Admin">Admin</option>
                                <option value="SuperAdmin">SuperAdmin</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Specific Email (shown when recipient_type is 'specific') -->
                        <div id="specific_email" class="mb-6 hidden">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter email address">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email Template -->
                        <div class="mb-6">
                            <label for="email_template" class="block text-sm font-medium text-gray-700 mb-2">Email Template</label>
                            <select name="email_template" id="email_template" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="custom">Custom Message</option>
                                <option value="announcement">📢 Announcement Template</option>
                                <option value="newsletter">📰 Newsletter Template</option>
                                <option value="reminder">⏰ Reminder Template</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Choose a template that matches your email type for better formatting and styling.</p>
                            @error('email_template')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div class="mb-6">
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <input type="text" name="subject" id="subject" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter email subject" value="{{ old('subject') }}" required>
                            @error('subject')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div class="mb-6">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea name="message" id="message" rows="8" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter your message here..." required>{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end space-x-3">
                            <button type="button" id="preview-btn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Preview
                            </button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Send Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Preview Modal -->
    <div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">Email Preview</h3>
                    <button type="button" id="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="mt-4">
                    <!-- Template Info -->
                    <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-800" id="templateInfo"></p>
                    </div>
                    
                    <!-- Email Preview Container -->
                    <div class="border rounded-lg overflow-hidden">
                        <!-- Email Header -->
                        <div class="bg-gray-50 px-4 py-3 border-b">
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <span>From: {{ config('mail.from.name') }} &lt;{{ config('mail.from.address') }}&gt;</span>
                                <span id="previewDate"></span>
                            </div>
                            <div class="mt-1">
                                <span class="text-sm text-gray-600">To: </span>
                                <span class="text-sm font-medium" id="recipientInfo"></span>
                            </div>
                            <div class="mt-2">
                                <span class="text-lg font-semibold text-gray-900" id="previewSubject"></span>
                            </div>
                        </div>
                        
                        <!-- Email Body -->
                        <div class="p-6" id="previewBody">
                            <div id="emailContent" class="prose max-w-none">
                                <!-- Dynamic content will be inserted here -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex items-center justify-end pt-4 border-t space-x-3">
                    <button type="button" id="closeModalBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Close
                    </button>
                    <button type="button" id="sendFromPreview" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Send Email
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for dynamic form fields and modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const recipientType = document.getElementById('recipient_type');
            const roleSelection = document.getElementById('role_selection');
            const specificEmail = document.getElementById('specific_email');
            const roleField = document.getElementById('role');
            const emailField = document.getElementById('email');
            const previewModal = document.getElementById('previewModal');
            const previewBtn = document.getElementById('preview-btn');
            const closeModal = document.getElementById('closeModal');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const sendFromPreview = document.getElementById('sendFromPreview');

            recipientType.addEventListener('change', function() {
                // Hide all conditional fields
                roleSelection.classList.add('hidden');
                specificEmail.classList.add('hidden');
                
                // Remove required attributes
                roleField.removeAttribute('required');
                emailField.removeAttribute('required');

                // Show relevant field based on selection
                if (this.value === 'role') {
                    roleSelection.classList.remove('hidden');
                    roleField.setAttribute('required', 'required');
                } else if (this.value === 'specific') {
                    specificEmail.classList.remove('hidden');
                    emailField.setAttribute('required', 'required');
                }
            });

            // Preview functionality
            previewBtn.addEventListener('click', function() {
                const template = document.getElementById('email_template').value;
                const subject = document.getElementById('subject').value;
                const message = document.getElementById('message').value;
                const recipientTypeValue = document.getElementById('recipient_type').value;
                const role = document.getElementById('role').value;
                const email = document.getElementById('email').value;
                
                if (subject && message) {
                    // Update template info
                    let templateInfo = '';
                    let templateClass = '';
                    switch(template) {
                        case 'announcement':
                            templateInfo = '📢 Announcement Template - Professional red design with social sharing options';
                            templateClass = 'announcement-template';
                            break;
                        case 'newsletter':
                            templateInfo = '📰 Newsletter Template - Clean blue design perfect for regular updates';
                            templateClass = 'newsletter-template';
                            break;
                        case 'reminder':
                            templateInfo = '⏰ Reminder Template - Orange design with urgency indicators';
                            templateClass = 'reminder-template';
                            break;
                        case 'custom':
                        default:
                            templateInfo = '📝 Custom Template - Simple, clean design for general messages';
                            templateClass = 'custom-template';
                            break;
                    }
                    
                    // Update modal content
                    document.getElementById('templateInfo').textContent = templateInfo;
                    document.getElementById('previewSubject').textContent = subject;
                    document.getElementById('previewDate').textContent = new Date().toLocaleString();
                    
                    // Update recipient info
                    let recipientText = '';
                    switch(recipientTypeValue) {
                        case 'all':
                            recipientText = 'All Users';
                            break;
                        case 'role':
                            recipientText = role ? `All ${role} users` : 'Select a role';
                            break;
                        case 'specific':
                            recipientText = email || 'Enter email address';
                            break;
                    }
                    document.getElementById('recipientInfo').textContent = recipientText;
                    
                    // Update email content with template styling
                    const emailContent = document.getElementById('emailContent');
                    emailContent.className = `prose max-w-none ${templateClass}`;
                    emailContent.innerHTML = `
                        <div class="email-greeting">
                            <p><strong>Hello [Recipient Name],</strong></p>
                        </div>
                        <div class="email-message">
                            ${message.replace(/\n/g, '<br>')}
                        </div>
                        <div class="email-footer mt-6 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600">
                                Best regards,<br>
                                {{ config('mail.from.name') }}<br>
                                <em>Sent on ${new Date().toLocaleDateString()}</em>
                            </p>
                        </div>
                    `;
                    
                    // Show modal
                    previewModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                } else {
                    alert('Please fill in the subject and message fields to preview.');
                }
            });

            // Close modal functionality
            function closeModalFunction() {
                previewModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            closeModal.addEventListener('click', closeModalFunction);
            closeModalBtn.addEventListener('click', closeModalFunction);

            // Close modal when clicking outside
            previewModal.addEventListener('click', function(e) {
                if (e.target === previewModal) {
                    closeModalFunction();
                }
            });

            // Send from preview
            sendFromPreview.addEventListener('click', function() {
                closeModalFunction();
                // Submit the form
                document.querySelector('form').submit();
            });

            // ESC key to close modal
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !previewModal.classList.contains('hidden')) {
                    closeModalFunction();
                }
            });
        });
    </script>

    <!-- Template-specific styles -->
    <style>
        .announcement-template {
            border-left: 4px solid #dc2626;
            padding-left: 1rem;
        }
        .announcement-template .email-greeting {
            color: #dc2626;
            font-weight: 600;
        }
        
        .newsletter-template {
            border-left: 4px solid #2563eb;
            padding-left: 1rem;
        }
        .newsletter-template .email-greeting {
            color: #2563eb;
            font-weight: 600;
        }
        
        .reminder-template {
            border-left: 4px solid #ea580c;
            padding-left: 1rem;
        }
        .reminder-template .email-greeting {
            color: #ea580c;
            font-weight: 600;
        }
        
        .custom-template {
            border-left: 4px solid #6b7280;
            padding-left: 1rem;
        }
        .custom-template .email-greeting {
            color: #374151;
            font-weight: 600;
        }
        
        .email-message {
            line-height: 1.6;
            margin: 1rem 0;
        }
        
        .email-footer {
            font-size: 0.875rem;
            color: #6b7280;
        }
    </style>
</x-admin-layout>