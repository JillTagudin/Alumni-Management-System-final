@php
    // Role protection at view level
    if (!Auth::user()->isHR()) {
        abort(403, 'Unauthorized access. HR role required.');
    }
@endphp

<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pending Job Postings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($pendingJobPostings->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Job Title
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Company
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Location
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Requested At
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pendingJobPostings as $change)
                                        @php
                                            $data = $change->change_data;
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $data['title'] ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $data['company'] ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $data['location'] ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    @if($change->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($change->status === 'approved') bg-green-100 text-green-800
                                                    @elseif($change->status === 'rejected') bg-red-100 text-red-800
                                                    @elseif($change->status === 'denied') bg-red-100 text-red-800
                                                    @endif">
                                                    {{ ucfirst($change->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $change->created_at->format('M d, Y g:i A') }}
                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button type="button" 
                                                        class="text-indigo-600 hover:text-indigo-900 view-details-btn"
                                                        data-change-id="{{ $change->id }}"
                                                        data-title="{{ $data['title'] ?? 'N/A' }}"
                                                        data-company="{{ $data['company'] ?? 'N/A' }}"
                                                        data-location="{{ $data['location'] ?? 'N/A' }}"
                                                        data-description="{{ $data['description'] ?? 'N/A' }}"
                                                        data-requirements="{{ $data['requirements'] ?? 'N/A' }}"
                                                        data-salary="{{ $data['salary'] ?? 'N/A' }}"
                                                        data-employment-type="{{ $data['employment_type'] ?? 'N/A' }}"
                                                        data-application-deadline="{{ $data['application_deadline'] ?? 'N/A' }}"
                                                        data-contact-email="{{ $data['contact_email'] ?? 'N/A' }}"
                                                        data-contact-number="{{ $data['contact_number'] ?? 'N/A' }}"
                                                        data-status="{{ $change->status }}"
                                                        data-requested-at="{{ $change->created_at->format('M d, Y g:i A') }}">
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $pendingJobPostings->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 text-lg">
                                <i class="fas fa-briefcase text-4xl mb-4"></i>
                                <p>No pending job postings found.</p>
                                <p class="text-sm mt-2">Your job posting requests will appear here once submitted for approval.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for viewing details -->
    <div id="detailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden flex items-center justify-center p-4 z-50">
        <div class="relative mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-md bg-white max-h-[80vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modal-title">Job Posting Details</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" id="closeModal">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Job Title</label>
                        <p class="mt-1 text-sm text-gray-900" id="detail-title"></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Company</label>
                        <p class="mt-1 text-sm text-gray-900" id="detail-company"></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Location</label>
                        <p class="mt-1 text-sm text-gray-900" id="detail-location"></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Employment Type</label>
                        <p class="mt-1 text-sm text-gray-900" id="detail-employment-type"></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Salary</label>
                        <p class="mt-1 text-sm text-gray-900" id="detail-salary"></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Application Deadline</label>
                        <p class="mt-1 text-sm text-gray-900" id="detail-deadline"></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contact Email</label>
                        <p class="mt-1 text-sm text-gray-900" id="detail-email"></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                        <p class="mt-1 text-sm text-gray-900" id="detail-contact-number"></p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <div class="mt-1 text-sm text-gray-900 whitespace-pre-wrap" id="detail-description"></div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Requirements</label>
                        <div class="mt-1 text-sm text-gray-900 whitespace-pre-wrap" id="detail-requirements"></div>
                    </div>
                    
                    <div class="border-t pt-4">
                        <div class="flex justify-between">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <span class="mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full" id="detail-status-badge"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Requested At</label>
                                <p class="mt-1 text-sm text-gray-900" id="detail-requested-at"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400" id="closeModalBtn">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('detailsModal');
            const closeModal = document.getElementById('closeModal');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const viewDetailsBtns = document.querySelectorAll('.view-details-btn');

            viewDetailsBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Populate modal with data
                    document.getElementById('detail-title').textContent = this.dataset.title;
                    document.getElementById('detail-company').textContent = this.dataset.company;
                    document.getElementById('detail-location').textContent = this.dataset.location;
                    document.getElementById('detail-description').innerHTML = this.dataset.description;
                    document.getElementById('detail-requirements').innerHTML = this.dataset.requirements;
                    document.getElementById('detail-salary').textContent = this.dataset.salary;
                    document.getElementById('detail-employment-type').textContent = this.dataset.employmentType;
                    document.getElementById('detail-deadline').textContent = this.dataset.applicationDeadline;
                    document.getElementById('detail-email').textContent = this.dataset.contactEmail;
                    document.getElementById('detail-contact-number').textContent = this.dataset.contactNumber;
                    document.getElementById('detail-requested-at').textContent = this.dataset.requestedAt;
                    
                    // Set status badge
                    const statusBadge = document.getElementById('detail-status-badge');
                    const status = this.dataset.status;
                    statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                    statusBadge.className = 'mt-1 inline-flex px-2 py-1 text-xs font-semibold rounded-full ';
                    
                    if (status === 'pending') {
                        statusBadge.className += 'bg-yellow-100 text-yellow-800';
                    } else if (status === 'approved') {
                        statusBadge.className += 'bg-green-100 text-green-800';
                    } else if (status === 'rejected') {
                        statusBadge.className += 'bg-red-100 text-red-800';
                    } else if (status === 'denied') {
                        statusBadge.className += 'bg-red-100 text-red-800';
                    }
                    
                    modal.classList.remove('hidden');
                });
            });

            closeModal.addEventListener('click', function() {
                modal.classList.add('hidden');
            });

            closeModalBtn.addEventListener('click', function() {
                modal.classList.add('hidden');
            });

            // Close modal when clicking outside
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
</x-admin-layout>