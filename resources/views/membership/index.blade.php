@if($user->role === 'Admin' || $user->role === 'Staff' || $user->role === 'SuperAdmin')
    <x-admin-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Alumni Membership Management') }}
            </h2>
        </x-slot>
        
        <div class="min-h-screen relative">
            <!-- Sticky Statistics Cards and Search -->
            <div class="sticky top-16 z-20 bg-transparent backdrop-blur-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-2">
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-4">
                    <div class="bg-white bg-opacity-80 backdrop-blur-sm overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Alumni</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $stats['total_alumni'] }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white bg-opacity-80 backdrop-blur-sm overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Active Members</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $stats['active_members'] }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white bg-opacity-80 backdrop-blur-sm overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Members</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ $stats['pending_members'] }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    
                    <!-- Sync Button Section -->
                    <div class="bg-white bg-opacity-80 backdrop-blur-sm shadow rounded-lg mb-4">
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Balance Update Sync</h3>
                                    <p class="text-sm text-gray-500">Sync membership data with balance update records</p>
                                </div>
                                <button id="syncButton" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-200">
                                    <i class="fas fa-sync-alt mr-2"></i>Sync with Balance Update
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Section -->
                    <div class="bg-white bg-opacity-80 backdrop-blur-sm shadow rounded-lg mb-4">
                        <div class="px-6 py-4">
                            <div class="flex flex-wrap gap-4">
                                <!-- Search Input -->
                                <div class="flex-1 min-w-64">
                                    <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                    <input type="text" id="searchInput" 
                                           placeholder="Search by name, student ID, or alumni ID..." 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <!-- Status Filter -->
                                <div class="min-w-48">
                                    <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-1">Membership Status</label>
                                    <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                                
                                <!-- Membership Type Filter -->
                                <div class="min-w-48">
                                    <label for="typeFilter" class="block text-sm font-medium text-gray-700 mb-1">Membership Type</label>
                                    <select id="typeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Types</option>
                                        <option value="Lifetime">Lifetime</option>
                                        <option value="Annual">Annual</option>
                                        <option value="Not set">Not Set</option>
                                    </select>
                                </div>
                                
                                <!-- Clear Filters Button -->
                                <div class="flex items-end">
                                    <button id="clearFilters" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition duration-200">
                                        <i class="fas fa-times mr-2"></i>Clear Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
            <!-- Scrollable Content Area -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-2">
                
                <!-- Alumni Membership Table -->
                <div class="bg-white bg-opacity-80 backdrop-blur-sm shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Alumni Membership Records</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage alumni membership status and types.</p>
                    </div>
                    
                    @if($alumni->count() > 0)
                        <div class="overflow-x-auto max-h-96 overflow-y-auto">
                            <table id="membershipTable" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Student ID
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Alumni ID
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Course
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Membership Type
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Payment Amount
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($alumni as $alumnus)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $alumnus->Fullname }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $alumnus->student_number ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $alumnus->AlumniID }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $alumnus->Course }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    @if($alumnus->membership_status === 'Active') bg-green-100 text-green-800
                                                    @elseif($alumnus->membership_status === 'Pending') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ $alumnus->membership_status }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    @if($alumnus->membership_type === 'Lifetime') bg-purple-100 text-purple-800
                                                    @elseif($alumnus->membership_type === 'Annual') bg-blue-100 text-blue-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $alumnus->membership_type }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($alumnus->payment_amount)
                                                    ₱{{ number_format($alumnus->payment_amount, 2) }}
                                                @else
                                                    <span class="text-gray-400">Not set</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button onclick="openEditModal({{ $alumnus->id }}, {{ json_encode($alumnus->Fullname) }}, {{ json_encode($alumnus->membership_status) }}, {{ json_encode($alumnus->membership_type) }})" 
                                                        class="text-indigo-600 hover:text-indigo-900">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No alumni found</h3>
                            <p class="mt-1 text-sm text-gray-500">No alumni records found in the database.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        

        
        <!-- Edit Membership Modal -->
        <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 9999;">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" style="z-index: 9999;">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Edit Membership</h3>
                    <form id="editForm">
                        <input type="hidden" id="alumniId">
                        
                        <div class="mb-4">
                            <label for="membershipStatus" class="block text-sm font-medium text-gray-700 mb-2">Membership Status</label>
                            <select id="membershipStatus" name="membership_status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="membershipType" class="block text-sm font-medium text-gray-700 mb-2">Membership Type</label>
                            <select id="membershipType" name="membership_type" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="Annual">Annual</option>
                                <option value="Lifetime">Lifetime</option>
                            </select>
                        </div>
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeEditModal()" 
                                    class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sync Result Modal -->
        <div id="syncModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 9999;">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" style="z-index: 9999;">
                <div class="mt-3">
                    <div class="flex items-center mb-4">
                        <div id="syncIcon" class="flex-shrink-0 w-10 h-10 mx-auto">
                            <!-- Success icon -->
                            <svg id="successIcon" class="w-10 h-10 text-green-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <!-- Error icon -->
                            <svg id="errorIcon" class="w-10 h-10 text-red-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 id="syncModalTitle" class="text-lg font-medium text-gray-900 mb-4 text-center">Sync Result</h3>
                    <div id="syncModalContent" class="mb-4 text-sm text-gray-600">
                        <!-- Content will be populated by JavaScript -->
                    </div>
                    <div class="flex justify-center">
                        <button type="button" onclick="closeSyncModal()" 
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            function openEditModal(id, name, status, type) {
                document.getElementById('alumniId').value = id;
                document.getElementById('modalTitle').textContent = 'Edit Membership - ' + name;
                document.getElementById('membershipStatus').value = status;
                document.getElementById('membershipType').value = type;
                document.getElementById('editModal').classList.remove('hidden');
            }
            
            function closeEditModal() {
                document.getElementById('editModal').classList.add('hidden');
            }
            
            function showSyncModal(isSuccess, title, message, details = null) {
                const modal = document.getElementById('syncModal');
                const titleEl = document.getElementById('syncModalTitle');
                const contentEl = document.getElementById('syncModalContent');
                const successIcon = document.getElementById('successIcon');
                const errorIcon = document.getElementById('errorIcon');
                
                // Set title and content
                titleEl.textContent = title;
                contentEl.innerHTML = message;
                
                // Add details if provided
                if (details) {
                    contentEl.innerHTML += '<div class="mt-3 p-3 bg-gray-50 rounded-md"><strong>Details:</strong><br>' + details + '</div>';
                }
                
                // Show appropriate icon
                if (isSuccess) {
                    successIcon.classList.remove('hidden');
                    errorIcon.classList.add('hidden');
                } else {
                    successIcon.classList.add('hidden');
                    errorIcon.classList.remove('hidden');
                }
                
                // Show modal
                modal.classList.remove('hidden');
            }
            
            function closeSyncModal() {
                document.getElementById('syncModal').classList.add('hidden');
            }
            
            document.getElementById('editForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const id = document.getElementById('alumniId').value;
                const status = document.getElementById('membershipStatus').value;
                const type = document.getElementById('membershipType').value;
                
                fetch(`/membership/${id}/update`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        membership_status: status,
                        membership_type: type
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        showSyncModal(false, 'Update Error', 'Error updating membership. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showSyncModal(false, 'Update Error', 'Error updating membership. Please try again.');
                });
            });
            
            // Search and filter functionality
            function applyFilters() {
                let searchFilter = document.getElementById("searchInput").value.toLowerCase().trim().split(/\s+/);
                let statusFilter = document.getElementById("statusFilter").value;
                let typeFilter = document.getElementById("typeFilter").value;
                let rows = document.querySelectorAll("#membershipTable tbody tr");

                rows.forEach(row => {
                    let columns = Array.from(row.getElementsByTagName("td"));
                    let rowText = columns.map(td => td.textContent.toLowerCase()).join(" ");
                    
                    // Get status and type from specific columns
                    let statusText = columns[4] ? columns[4].textContent.trim() : '';
                    let typeText = columns[5] ? columns[5].textContent.trim() : '';

                    // Check search keywords
                    let searchMatch = searchFilter.length === 0 || searchFilter.every(keyword => rowText.includes(keyword));
                    
                    // Check status filter
                    let statusMatch = !statusFilter || statusText === statusFilter;
                    
                    // Check type filter
                    let typeMatch = !typeFilter || typeText === typeFilter;

                    // Show row only if all filters match
                    row.style.display = (searchMatch && statusMatch && typeMatch) ? "" : "none";
                });
            }

            // Add event listeners for all filters
            document.getElementById("searchInput").addEventListener("keyup", applyFilters);
            document.getElementById("statusFilter").addEventListener("change", applyFilters);
            document.getElementById("typeFilter").addEventListener("change", applyFilters);
            
            // Clear filters functionality
            document.getElementById("clearFilters").addEventListener("click", function() {
                document.getElementById("searchInput").value = '';
                document.getElementById("statusFilter").value = '';
                document.getElementById("typeFilter").value = '';
                applyFilters();
            });
        </script>
    </x-admin-layout>
@else
    <!-- ... existing alumni user view ... -->
@endif


<!-- Sync Button JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const syncButton = document.getElementById('syncButton');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    syncButton.addEventListener('click', function() {
        // Disable button and show loading state
        syncButton.disabled = true;
        syncButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Syncing...';
        syncButton.classList.add('opacity-50', 'cursor-not-allowed');
        
        // Make AJAX request to sync endpoint
        fetch('/membership/sync', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let message = `Success: ${data.message}<br><br><strong>Updated Records:</strong> ${data.updated_count}`;
                let details = '';
                
                // Optionally show detailed results
                if (data.updated_records && data.updated_records.length > 0) {
                    details = 'Updated Alumni:<br>';
                    data.updated_records.forEach(record => {
                        details += `• ${record.fullname} (${record.alumni_id}): ₱${record.payment_amount}<br>`;
                    });
                }
                
                showSyncModal(true, 'Sync Successful', message, details);
                
                // Refresh the page after modal is closed
                setTimeout(() => {
                    location.reload();
                }, 3000);
            } else {
                showSyncModal(false, 'Sync Error', `Error: ${data.message}`);
            }
        })
        .catch(error => {
            console.error('Sync error:', error);
            showSyncModal(false, 'Sync Error', 'Failed to sync data. Please try again.');
        })
        .finally(() => {
            // Re-enable button
            syncButton.disabled = false;
            syncButton.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Sync with Balance Update';
            syncButton.classList.remove('opacity-50', 'cursor-not-allowed');
        });
    });
});
</script>