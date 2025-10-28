<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pending Changes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($pendingChanges->count() > 0)
                        <!-- Search Bar -->
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-4 items-center">
                                <!-- Search Input -->
                                <div class="flex-1 min-w-64">
                                    <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Search Current Page</label>
                                    <input type="text" 
                                           id="searchInput" 
                                           placeholder="Search current page..." 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                           onkeyup="searchTable()">
                                </div>
                            </div>
                        </div>

                        <!-- Date Filters Section -->
                        <div class="bg-white bg-opacity-80 backdrop-blur-sm shadow rounded-lg mb-4">
                            <div class="px-6 py-4">
                                @if(request()->hasAny(['date_from', 'date_to']))
                                    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="text-sm font-medium text-blue-800">Active Filters</h4>
                                                <div class="text-sm text-blue-600">
                                                    @if(request('date_from'))
                                                        <span>From: <strong>{{ request('date_from') }}</strong></span>
                                                    @endif
                                                    @if(request('date_to'))
                                                        <span class="ml-2">To: <strong>{{ request('date_to') }}</strong></span>
                                                    @endif
                                                </div>
                                            </div>
                                            <a href="{{ route('staff.pending-changes') }}" class="text-blue-600 hover:text-blue-800 text-sm underline">Clear All Filters</a>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Date Filter Form -->
                                <form method="GET" action="{{ route('staff.pending-changes') }}" class="flex flex-wrap gap-4 items-end">
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                                        <input type="date" name="date_from" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" value="{{ request('date_from') }}" onchange="this.form.submit()">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                                        <input type="date" name="date_to" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" value="{{ request('date_to') }}" onchange="this.form.submit()">
                                    </div>
                                    
                                    @if(request()->hasAny(['date_from', 'date_to']))
                                        <div>
                                            <a href="{{ route('staff.pending-changes') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-200">Clear Date Filters</a>
                                        </div>
                                    @endif
                                </form>
                            </div>
                        </div>

                        <!-- Real-time update indicator -->
                        <div id="refresh-indicator" class="hidden mb-4">
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                                <div class="flex items-center">
                                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-2"></div>
                                    <p class="text-blue-700 text-sm">Updating pending changes...</p>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-300" id="pendingChangesTable">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="py-2 px-4 border-b text-left">Change Type</th>
                                        <th class="py-2 px-4 border-b text-left">Description</th>
                                        <th class="py-2 px-4 border-b text-left">Status</th>
                                        <th class="py-2 px-4 border-b text-left">Requested At</th>
                                        <th class="py-2 px-4 border-b text-left">Reviewed At</th>
                                        <th class="py-2 px-4 border-b text-left">Review Notes</th>
                                    </tr>
                                </thead>
                                <tbody id="pending-changes-tbody">
                                    @foreach($pendingChanges as $change)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-2 px-4 border-b">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucfirst(str_replace('_', ' ', $change->change_type)) }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                {{ $change->change_description }}
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                @if($change->status === 'pending')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3"/>
                                                        </svg>
                                                        Pending
                                                    </span>
                                                @elseif($change->status === 'approved')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3"/>
                                                        </svg>
                                                        Approved
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                            <circle cx="4" cy="4" r="3"/>
                                                        </svg>
                                                        Denied
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                {{ $change->created_at->format('M d, Y g:i A') }}
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    @if($change->reviewed_at)
                                                        {{ $change->reviewed_at->format('M d, Y g:i A') }}
                                                    @if($change->reviewedBy)
                                                        <br>
                                                        <small class="text-gray-500">by {{ $change->reviewedBy->name }}</small>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                @if($change->review_notes)
                                                    <div class="max-w-xs">
                                                        <p class="text-sm text-gray-700 truncate" title="{{ $change->review_notes }}">
                                                            {{ $change->review_notes }}
                                                        </p>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $pendingChanges->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 text-lg">
                                <i class="fas fa-inbox text-4xl mb-4"></i>
                                <p>You have no pending changes.</p>
                                <p class="text-sm mt-2">Any changes you request will appear here for tracking.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-refresh for real-time updates -->
    <script>
        let updateInterval;
        let isOnline = navigator.onLine;
        let isVisible = !document.hidden;
        let lastUpdateTime = Date.now();
        
        // Search function for current page
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('pendingChangesTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    const cell = cells[j];
                    if (cell && cell.textContent.toLowerCase().includes(filter)) {
                        found = true;
                        break;
                    }
                }

                row.style.display = found ? '' : 'none';
            }
        }

        // Real-time update function
        function updatePendingChanges() {
            if (!isOnline || !isVisible) return;
            
            const indicator = document.getElementById('refresh-indicator');
            const tbody = document.getElementById('pending-changes-tbody');
            
            // Show loading indicator
            if (indicator) {
                indicator.classList.remove('hidden');
            }
            
            // Include date filters if present
            const dateFromParam = document.getElementById('dateFromInput')?.value || '';
            const dateToParam = document.getElementById('dateToInput')?.value || '';
            const url = new URL('/api/staff/pending-changes', window.location.origin);
            if (dateFromParam) {
                url.searchParams.append('date_from', dateFromParam);
            }
            if (dateToParam) {
                url.searchParams.append('date_to', dateToParam);
            }
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Update table content
                if (tbody && data.html) {
                    tbody.innerHTML = data.html;
                }
                
                // Update pagination if provided
                if (data.pagination) {
                    const paginationContainer = document.querySelector('.pagination-container');
                    if (paginationContainer) {
                        paginationContainer.innerHTML = data.pagination;
                    }
                }
                
                // Update pending count badge if exists
                if (data.pendingCount !== undefined) {
                    const countBadges = document.querySelectorAll('.pending-count-badge');
                    countBadges.forEach(badge => {
                        badge.textContent = data.pendingCount;
                        badge.style.display = data.pendingCount > 0 ? 'inline' : 'none';
                    });
                }
                
                lastUpdateTime = Date.now();
            })
            .catch(error => {
                console.error('Error updating pending changes:', error);
            })
            .finally(() => {
                // Hide loading indicator
                if (indicator) {
                    indicator.classList.add('hidden');
                }
            });
        }
        
        // Set online status indicator
        function setOnlineStatus(online) {
            isOnline = online;
            const statusIndicator = document.querySelector('.online-status');
            if (statusIndicator) {
                statusIndicator.className = online ? 
                    'online-status w-2 h-2 bg-green-400 rounded-full' : 
                    'online-status w-2 h-2 bg-red-400 rounded-full';
            }
        }
        
        // Start/stop updates based on visibility and connection
        function manageUpdateInterval() {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
            
            if (isOnline && isVisible) {
                // Update immediately if it's been more than 30 seconds
                if (Date.now() - lastUpdateTime > 30000) {
                    updatePendingChanges();
                }
                
                // Set up regular updates every 10 seconds
                updateInterval = setInterval(updatePendingChanges, 10000);
            }
        }
        
        // Event listeners
        document.addEventListener('visibilitychange', function() {
            isVisible = !document.hidden;
            manageUpdateInterval();
        });
        
        window.addEventListener('online', function() {
            setOnlineStatus(true);
            manageUpdateInterval();
        });
        
        window.addEventListener('offline', function() {
            setOnlineStatus(false);
            manageUpdateInterval();
        });
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            setOnlineStatus(navigator.onLine);
            manageUpdateInterval();
        });
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
        });
    </script>
</x-admin-layout>