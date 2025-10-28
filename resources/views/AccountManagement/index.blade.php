<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Account Records') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Users</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $users->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Active Accounts</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $users->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Admin Users</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $users->where('role', 'Admin')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1 1v-2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Staff Users</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $users->where('role', 'Staff')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Header with Controls -->
            <div class="sticky top-0 z-40 bg-white/80 backdrop-blur-sm border-b border-gray-200 mb-6">
                <div class="px-6 py-4">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <!-- Left Controls -->
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Role Filter Toggles -->
                            <div class="flex flex-wrap gap-2">
                                <button class="toggle-btn px-3 py-1 text-xs font-medium rounded-full border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 active" onclick="toggleRoleFilter('all')">
                                    All Roles
                                </button>
                                <button class="toggle-btn px-3 py-1 text-xs font-medium rounded-full border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500" onclick="toggleRoleFilter('Admin')">
                                    Admin
                                </button>
                                <button class="toggle-btn px-3 py-1 text-xs font-medium rounded-full border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500" onclick="toggleRoleFilter('Staff')">
                                    Staff
                                </button>
                                <button class="toggle-btn px-3 py-1 text-xs font-medium rounded-full border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500" onclick="toggleRoleFilter('Alumni')">
                                    Alumni
                                </button>
                            </div>
                        </div>

                        <!-- Right Controls -->
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Search user accounts..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-18 0 7 7 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session()->get('success') }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Content -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-hidden">
                    <!-- Desktop Table View -->
                    <div class="table-container overflow-x-auto">
                        <div class="max-h-96 overflow-y-auto">
                            <table id="accountTable" class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STUDENT/EMPLOYEE ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Role</th>
                                        @if(auth()->user()->role === 'SuperAdmin')
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($users as $user)
                                    <tr class="hover:bg-gray-50" data-role="{{ $user->role ?? 'Alumni' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->student_number ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ 
                                                $user->role === 'Admin' ? 'bg-blue-100 text-blue-800' : 
                                                ($user->role === 'Staff' ? 'bg-orange-100 text-orange-800' : 
                                                ($user->role === 'HR' ? 'bg-green-100 text-green-800' : 
                                                ($user->role === 'SuperAdmin' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) 
                                            }}">
                                                {{ $user->role ?? 'Alumni' }}
                                            </span>
                                        </td>
                                        @if(auth()->user()->role === 'SuperAdmin')
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="mt-3">
                                                @if(!in_array($user->role, ['SuperAdmin']))
                                                    <form action="{{ route('AccountManagement.updateRole', $user) }}" method="POST" class="flex items-center space-x-2" id="roleForm-{{ $user->id }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="role" class="border border-gray-300 rounded-md text-sm px-2 py-1 w-full">
                                                            <option value="Alumni" {{ $user->role === 'Alumni' ? 'selected' : '' }}>Alumni</option>
                                                            <option value="Staff" {{ $user->role === 'Staff' ? 'selected' : '' }}>Staff</option>
                                                            <option value="HR" {{ $user->role === 'HR' ? 'selected' : '' }}>HR</option>
                                                            <option value="Admin" {{ $user->role === 'Admin' ? 'selected' : '' }}>Admin</option>
                                                            <option value="SuperAdmin" {{ $user->role === 'SuperAdmin' ? 'selected' : '' }}>SuperAdmin</option>
                                                        </select>
                                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                            Update
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="card-container hidden lg:hidden">
                        @foreach($users as $user)
                        <div class="user-card bg-white border border-gray-200 rounded-lg p-6 mb-4 shadow-sm" data-role="{{ $user->role ?? 'Alumni' }}">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ 
                                    $user->role === 'Admin' ? 'bg-blue-100 text-blue-800' : 
                                    ($user->role === 'Staff' ? 'bg-orange-100 text-orange-800' : 
                                    ($user->role === 'HR' ? 'bg-green-100 text-green-800' : 
                                    ($user->role === 'SuperAdmin' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) 
                                }}">
                                    {{ $user->role ?? 'Alumni' }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 gap-4 mb-4">
                                <div>
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">STUDENT/EMPLOYEE ID</span>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->student_number ?? 'N/A' }}</p>
                                </div>
                            </div>

                            @if(!in_array($user->role, ['SuperAdmin']) && auth()->user()->role === 'SuperAdmin')
                                <form action="{{ route('AccountManagement.updateRole', $user) }}" method="POST" class="flex items-center space-x-2" id="roleForm-mobile-{{ $user->id }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                        <option value="Alumni" {{ ($user->role === 'Alumni' || !$user->role) ? 'selected' : '' }}>Alumni</option>
                                        <option value="Staff" {{ $user->role === 'Staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="HR" {{ $user->role === 'HR' ? 'selected' : '' }}>HR</option>
                                        <option value="Admin" {{ $user->role === 'Admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="SuperAdmin" {{ $user->role === 'SuperAdmin' ? 'selected' : '' }}>SuperAdmin</option>
                                    </select>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Update
                                    </button>
                                </form>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Staff Approval Modal for Staff Users -->
    @if(auth()->user()->role === 'Staff')
        <x-staff-approval-modal />
    @endif

    <script>
        let currentRoleFilter = 'all';

        // Search functionality
        document.getElementById("searchInput").addEventListener("keyup", function () {
            let filter = this.value.toLowerCase().trim().split(/\s+/);
            filterTable(filter, currentRoleFilter);
        });

        // Role filter functionality
        function toggleRoleFilter(role) {
            // Update button states
            document.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-blue-500', 'text-white');
                btn.classList.add('bg-white', 'text-gray-700');
            });
            
            event.target.classList.add('active', 'bg-blue-500', 'text-white');
            event.target.classList.remove('bg-white', 'text-gray-700');
            
            currentRoleFilter = role;
            let searchFilter = document.getElementById("searchInput").value.toLowerCase().trim().split(/\s+/);
            filterTable(searchFilter, role);
        }

        function filterTable(searchFilter, roleFilter) {
            let rows = document.querySelectorAll("#accountTable tbody tr");
            let cards = document.querySelectorAll(".user-card");

            // Filter table rows
            rows.forEach(row => {
                let columns = Array.from(row.getElementsByTagName("td"));
                let rowText = columns.map(td => td.textContent.toLowerCase()).join(" ");
                let userRole = row.getAttribute('data-role');

                let searchMatch = searchFilter.length === 0 || searchFilter.every(keyword => rowText.includes(keyword));
                let roleMatch = roleFilter === 'all' || userRole === roleFilter;

                row.style.display = (searchMatch && roleMatch) ? "" : "none";
            });

            // Filter cards
            cards.forEach(card => {
                let cardText = card.textContent.toLowerCase();
                let userRole = card.getAttribute('data-role');

                let searchMatch = searchFilter.length === 0 || searchFilter.every(keyword => cardText.includes(keyword));
                let roleMatch = roleFilter === 'all' || userRole === roleFilter;

                card.style.display = (searchMatch && roleMatch) ? "" : "none";
            });
        }

        @if(auth()->user()->role === 'Staff')
        function handleStaffRoleUpdate(userId) {
            // Get the form and form data
            const form = document.getElementById('roleForm-' + userId) || document.getElementById('roleForm-mobile-' + userId);
            const formData = new FormData(form);
            
            // Add the CSRF token to FormData (it should already be there from the form)
            if (!formData.has('_token')) {
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            }
            
            // Submit the form via AJAX to create pending change
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show staff approval modal
                    showStaffApprovalModal();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error occurred'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while processing your request.');
            });
        }
        @else
        // Simple solution: Force page refresh after any form submission
        document.addEventListener('DOMContentLoaded', function() {
            // Add timestamp to prevent caching
            const timestamp = new Date().getTime();
            
            // Handle all role update forms
            const forms = document.querySelectorAll('form[action*="updateRole"]');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Let the form submit normally, then force refresh
                    setTimeout(() => {
                        // Add cache-busting parameter and reload
                        const currentUrl = window.location.href.split('?')[0];
                        window.location.href = currentUrl + '?refresh=' + timestamp;
                    }, 500);
                });
            });
        });
        @endif

        // Auto-hide success message
        setTimeout(() => {
            let successMessage = document.querySelector(".bg-green-50");
            if (successMessage) {
                successMessage.style.transition = "opacity 0.5s";
                successMessage.style.opacity = "0";
                setTimeout(() => successMessage.remove(), 500);
            }
        }, 3000);

        // Responsive view toggle (for future mobile optimization)
        function toggleViewMode() {
            const tableContainer = document.querySelector('.table-container');
            const cardContainer = document.querySelector('.card-container');
            
            if (window.innerWidth < 1024) {
                tableContainer.classList.add('hidden');
                cardContainer.classList.remove('hidden');
            } else {
                tableContainer.classList.remove('hidden');
                cardContainer.classList.add('hidden');
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleViewMode();
        });

        // Handle window resize
        window.addEventListener('resize', toggleViewMode);
    </script>
</x-admin-layout>