<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Announcement Generator') }}
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

                    <form method="POST" action="{{ route('admin.announcement.store') }}" class="space-y-6" enctype="multipart/form-data" id="announcementForm">
                        @csrf
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Announcement Title</label>
                            <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="general">General</option>
                                <option value="important">Important</option>
                                <option value="event">Event</option>
                                <option value="notice">Notice</option>
                                <option value="job_offers">Job Offers</option>
                                <option value="scholarship">Scholarship</option>
                            </select>
                        </div>

                        <div>

                            <div id="editor" style="height: 400px;"></div>
                            <textarea name="content" id="content" style="display: none;"></textarea>
                        </div>

                        <div>
                            <label for="attachments" class="block text-sm font-medium text-gray-700">File Attachments</label>
                            <input type="file" name="attachments[]" id="attachments" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.mp3,.wav,.ogg,.mp4,.avi,.mov" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <p class="mt-1 text-sm text-gray-500">Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG, GIF, MP3, WAV, OGG, MP4, AVI, MOV (Max 50MB each)</p>
                        </div>



                        <div class="flex items-center justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Announcement
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

        // Update hidden textarea when content changes
        quill.on('text-change', function() {
            document.getElementById('content').value = quill.root.innerHTML;
        });

        // Handle form submission
        document.querySelector('#announcementForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Update hidden textarea with Quill content
            document.getElementById('content').value = quill.root.innerHTML;
            
            // Create FormData object for file uploads
            const formData = new FormData(this);
            
            // Submit via AJAX
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessModal(data.message);
                } else {
                    alert('Error: ' + (data.message || 'Something went wrong'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                
                showSuccessModal('Your announcement has been submitted for admin approval!');
            });
        });
        
        function showSuccessModal(message) {
            // Create modal HTML
            const modalHTML = `
                <div id="successModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3 text-center">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mt-4">Success!</h3>
                            <p class="text-sm text-gray-500 mt-2">${message}</p>
                            <div class="flex justify-center mt-6">
                                <button onclick="closeSuccessModal()" 
                                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    OK
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to page
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }
        
        function closeSuccessModal() {
            const modal = document.getElementById('successModal');
            if (modal) {
                modal.remove();
                // Reset form
                document.getElementById('announcementForm').reset();
                quill.setContents([]);
            }
        }
    </script>
</x-admin-layout>
