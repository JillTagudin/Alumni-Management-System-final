<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Activity Logs') }}
        </h2>
    </x-slot>

    <!-- Add CSRF meta tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .show-ip-btn {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .show-ip-btn:hover {
            background-color: #1d4ed8;
        }

        .btn {
            padding: 6px 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-sm {
            padding: 4px 8px;
            font-size: 11px;
        }

        .btn-info {
            background-color: #10b981;
            color: white;
        }

        .btn-info:hover {
            background-color: #059669;
        }

        .ip-display {
            display: flex;
            align-items: center;
            gap: 8px;
            min-width: 80px; /* Minimum width for compact display */
            transition: all 0.3s ease;
        }

        .ip-display.expanded {
            min-width: 140px; /* Expanded width for full IP */
        }

        .masked-ip {
            color: #6b7280;
            font-style: italic;
            font-size: 12px;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .real-ip {
            display: none;
            color: #1f2937;
            font-weight: 500;
            font-size: 13px;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .real-ip.visible {
            display: inline;
        }

        .ip-toggle-btn {
            padding: 2px 6px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 3px;
            font-size: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .ip-toggle-btn:hover {
            background-color: #2563eb;
        }

        .ip-toggle-btn.hide-btn {
            background-color: #ef4444;
        }

        .ip-toggle-btn.hide-btn:hover {
            background-color: #dc2626;
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            width: 400px;
            max-width: 90%;
            max-height: 90%;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .modal-header h3 {
            margin: 0;
            color: #1f2937;
            font-size: 18px;
            font-weight: 600;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #6b7280;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-modal:hover {
            color: #374151;
        }

        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #374151;
            font-weight: 500;
        }

        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group input[type="password"]:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
        }

        .verify-btn {
            width: 100%;
            padding: 12px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .verify-btn:hover {
            background-color: #1d4ed8;
        }

        .error-message {
            color: #dc2626;
            margin: 10px 0;
            text-align: center;
            display: none;
        }

        .action-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .action-login { background-color: #10b981; color: white; }
        .action-logout { background-color: #f59e0b; color: white; }
        .action-create { background-color: #3b82f6; color: white; }
        .action-update { background-color: #8b5cf6; color: white; }
        .action-delete { background-color: #ef4444; color: white; }
        .action-view { background-color: #6b7280; color: white; }
        .action-hide { background-color: #374151; color: white; }

        .role-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .role-admin { background-color: #dc2626; color: white; }
        .role-staff { background-color: #2563eb; color: white; }
        .role-user { background-color: #059669; color: white; }
        .role-hr { background-color: #f59e0b; color: white; }
        .role-superadmin { background-color: #7c3aed; color: white; }
        .role-alumni { background-color: #10b981; color: white; }

        .success-message {
            background-color: #d1fae5;
            color: #065f46;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #10b981;
        }

        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
            .flex {
                flex-direction: column;
            }
            .gap-4 {
                gap: 1rem;
            }
        }
    </style>

    <div class="min-h-screen relative">
        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <!-- Search and Filters Section -->
            <div class="bg-white bg-opacity-80 backdrop-blur-sm shadow rounded-lg mb-4">
                <div class="px-6 py-4">
                    @if(session('success'))
                        <div class="success-message mb-4" id="success-message">
                            {{ session('success') }}
                            @if(request('global_search'))
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800">Global Search Results</h4>
                                    <p class="text-sm text-blue-600">Showing results for: "<strong>{{ request('global_search') }}</strong>"</p>
                                </div>
                                <a href="{{ route('activity-logs.index') }}" class="text-blue-600 hover:text-blue-800 text-sm underline">Clear Search</a>
                            </div>
                        </div>
                    @endif
                </div>
                    @endif
                    
                    <div class="flex flex-wrap gap-4 items-center">
                        <!-- Search Input -->
                        <div class="flex-1 min-w-64">
                            <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <div class="flex gap-2">
                                <input type="text" id="searchInput" 
                                       placeholder="Search current page..." 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       onkeyup="searchTable()">
                                <button type="button" 
                                        onclick="searchAllRecords()" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200 whitespace-nowrap"
                                        title="Search across all activity log records">
                                    Search All
                                </button>
                            </div>
                        </div>
                        
                        <!-- Show IP Button -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                            <button id="showIpBtn" class="show-ip-btn" onclick="showPasswordModal()">Show IP Addresses</button>
                        </div>
                    </div>
                    
                    <!-- Filters Form -->
                    <form method="GET" action="{{ route('activity-logs.index') }}" class="mt-4" id="filtersForm">
                        <input type="hidden" name="global_search" id="globalSearchInput" value="{{ request('global_search') }}">
                        <div class="flex flex-wrap gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Role Filter</label>
                                <select name="role_filter" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                                    <option value="">All Roles</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" {{ request('role_filter') == $role ? 'selected' : '' }}>
                                            {{ $role }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">User Filter</label>
                                <select name="user_filter" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_filter') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                                <input type="date" name="date_from" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" value="{{ request('date_from') }}" onchange="this.form.submit()">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                                <input type="date" name="date_to" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" value="{{ request('date_to') }}" onchange="this.form.submit()">
                            </div>
                            
                            @if(request()->hasAny(['role_filter', 'user_filter', 'date_from', 'date_to', 'global_search']))
                                <div>
                                    <a href="{{ route('activity-logs.index') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-200">Clear Filters</a>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Activity Logs Table -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white bg-opacity-80 backdrop-blur-sm shadow rounded-lg">
                <div class="overflow-x-auto">
                    <table id="activityTable" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ip-column">IP Address</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($logs as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>{{ $log->created_at->setTimezone('Asia/Manila')->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $log->created_at->setTimezone('Asia/Manila')->format('h:i:s A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $log->user ? $log->user->name : 'System' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($log->user)
                                            <span class="role-badge role-{{ strtolower($log->user->role) }}">
                                                {{ strtoupper($log->user->role) }}
                                            </span>
                                        @else
                                            <span class="role-badge">
                                                SYSTEM
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="action-badge action-{{ $log->action }}">
                                            {{ strtoupper($log->action) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $log->description }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 ip-column">
                                        <div class="ip-display" id="ip-display-{{ $log->id }}">
                                            <span class="masked-ip" id="masked-ip-{{ $log->id }}">
                                                {{ str_repeat('*', max(1, strlen($log->ip_address) - 4)) }}{{ substr($log->ip_address, -2) }}
                                            </span>
                                            <span class="real-ip" id="real-ip-{{ $log->id }}">{{ $log->ip_address }}</span>
                                            <button 
                                                onclick="toggleSingleIP({{ $log->id }}, '{{ $log->ip_address }}')" 
                                                class="ip-toggle-btn" 
                                                id="toggle-btn-{{ $log->id }}"
                                                title="Click to view full IP address">
                                                View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No activity logs found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($logs->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $logs->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Password Modal (keeping all existing functionality) -->
    <div id="passwordModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Password Verification Required</h3>
                <button class="close-modal" onclick="closePasswordModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p style="margin-bottom: 15px; color: #1f2937; font-weight: 500;">
                    <span id="modalMessage">Please enter your password to view IP addresses:</span>
                </p>
                <div class="form-group">
                    <label for="modalPassword">Password:</label>
                    <x-password-input 
                        id="modalPassword" 
                        name="modalPassword" 
                        placeholder="Enter your password"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>
                <div class="error-message" id="passwordError"></div>
                <button class="verify-btn" onclick="verifyPassword()">Verify Password</button>
            </div>
        </div>
    </div>

    <!-- Details Modal (keeping all existing functionality) -->
    <div id="detailsModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Activity Log Details</h3>
                <button class="close-modal" onclick="closeDetailsModal()">&times;</button>
            </div>
            <div class="modal-body" id="detailsContent">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- IP Details Modal -->
    <div id="ipModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>IP Address Details</h3>
                <button class="modal-close" onclick="closeIPModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="ipContent"></div>
            </div>
        </div>
    </div>

    <!-- All existing JavaScript functions preserved -->
    <script>
        // All your existing JavaScript functions remain exactly the same
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('activityTable');
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

        function showPasswordModal() {
            // Set appropriate message based on context
            const modalMessage = document.querySelector('#modalMessage');
            if (pendingLogId) {
                modalMessage.textContent = 'Please enter your password to view this IP address:';
            } else {
                modalMessage.textContent = 'Please enter your password to view all IP addresses:';
            }
            
            // Clear any previous errors
            const errorDiv = document.getElementById('passwordError');
            if (errorDiv) {
                errorDiv.style.display = 'none';
            }
            
            document.getElementById('passwordModal').style.display = 'flex';
            document.getElementById('modalPassword').focus();
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
            document.getElementById('modalPassword').value = '';
            document.getElementById('passwordError').style.display = 'none';
        }

        function verifyPassword() {
            const password = document.getElementById('modalPassword').value;
            const errorDiv = document.getElementById('passwordError');
            
            if (!password) {
                errorDiv.textContent = 'Please enter your password.';
                errorDiv.style.display = 'block';
                return;
            }
            
            fetch('/verify-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ password: password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show all real IP addresses
                    document.querySelectorAll('.masked-ip').forEach(el => el.style.display = 'none');
                    document.querySelectorAll('.real-ip').forEach(el => el.style.display = 'inline');
                    
                    // Change button text
                    const showBtn = document.getElementById('showIpBtn');
                    showBtn.textContent = 'Hide IP Addresses';
                    showBtn.setAttribute('onclick', 'hideIpAddresses()');
                    
                    closePasswordModal();
                } else {
                    errorDiv.textContent = data.message || 'Incorrect password. Please try again.';
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.style.display = 'block';
            });
        }

        function hideIpAddresses() {
            isPasswordVerified = false;
            verifiedIPs.clear();
            
            // Hide all real IP addresses and show masked ones
            document.querySelectorAll('.real-ip').forEach(el => el.classList.remove('visible'));
            document.querySelectorAll('.masked-ip').forEach(el => el.style.display = 'inline');
            document.querySelectorAll('.ip-toggle-btn').forEach(btn => {
                btn.textContent = 'View';
                btn.classList.remove('hide-btn');
                btn.title = 'Click to view full IP address';
            });
            document.querySelectorAll('.ip-display').forEach(container => {
                container.classList.remove('expanded');
            });
            
            // Change button text back
            const showBtn = document.getElementById('showIpBtn');
            if (showBtn) {
                showBtn.textContent = 'Show IP Addresses';
                showBtn.setAttribute('onclick', 'showPasswordModal()');
            }
        }

        // Global variables
        let pendingLogId = null;
        let isPasswordVerified = false;
        let verifiedIPs = new Set(); // Track which IPs have been verified

        function toggleSingleIP(logId, ipAddress) {
            if (isPasswordVerified || verifiedIPs.has(logId)) {
                // If already verified, just toggle the display
                toggleIPDisplay(logId);
            } else {
                // Need password verification first
                pendingLogId = logId;
                showPasswordModal();
            }
        }

        function toggleIPDisplay(logId) {
            const maskedElement = document.getElementById(`masked-ip-${logId}`);
            const realElement = document.getElementById(`real-ip-${logId}`);
            const toggleBtn = document.getElementById(`toggle-btn-${logId}`);
            const displayContainer = document.getElementById(`ip-display-${logId}`);
            
            if (realElement.classList.contains('visible')) {
                // Hide real IP, show masked
                realElement.classList.remove('visible');
                maskedElement.style.display = 'inline';
                toggleBtn.textContent = 'View';
                toggleBtn.classList.remove('hide-btn');
                toggleBtn.title = 'Click to view full IP address';
                displayContainer.classList.remove('expanded');
            } else {
                // Show real IP, hide masked
                maskedElement.style.display = 'none';
                realElement.classList.add('visible');
                toggleBtn.textContent = 'Hide';
                toggleBtn.classList.add('hide-btn');
                toggleBtn.title = 'Click to hide IP address';
                displayContainer.classList.add('expanded');
            }
        }

        function verifyPassword() {
            const password = document.getElementById('modalPassword').value;
            const errorDiv = document.getElementById('passwordError');
            
            if (!password) {
                errorDiv.textContent = 'Please enter your password.';
                errorDiv.style.display = 'block';
                return;
            }
            
            fetch('/verify-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ password: password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (pendingLogId) {
                        // Mark this specific IP as verified and show it
                        verifiedIPs.add(pendingLogId);
                        toggleIPDisplay(pendingLogId);
                        pendingLogId = null;
                    } else {
                        // Global verification - mark all as verified
                        isPasswordVerified = true;
                        // Show all real IP addresses
                        document.querySelectorAll('.masked-ip').forEach(el => el.style.display = 'none');
                        document.querySelectorAll('.real-ip').forEach(el => el.classList.add('visible'));
                        document.querySelectorAll('.ip-toggle-btn').forEach(btn => {
                            btn.textContent = 'Hide';
                            btn.classList.add('hide-btn');
                            btn.title = 'Click to hide IP address';
                        });
                        document.querySelectorAll('.ip-display').forEach(container => {
                            container.classList.add('expanded');
                        });
                        
                        // Change main button text
                        const showBtn = document.getElementById('showIpBtn');
                        if (showBtn) {
                            showBtn.textContent = 'Hide IP Addresses';
                            showBtn.setAttribute('onclick', 'hideIpAddresses()');
                        }
                    }
                    
                    closePasswordModal();
                } else {
                    errorDiv.textContent = data.message || 'Incorrect password. Please try again.';
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.style.display = 'block';
            });
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
            document.getElementById('modalPassword').value = '';
            document.getElementById('passwordError').style.display = 'none';
            pendingLogId = null;
        }

        function viewRecordDetails(logId) {
            fetch(`/activity-logs/${logId}/details`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('detailsContent').innerHTML = `
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                            <div>
                                <strong>User:</strong><br>
                                ${data.user || 'System'}
                            </div>
                            <div>
                                <strong>Action:</strong><br>
                                ${data.action}
                            </div>
                            <div>
                                <strong>Date & Time:</strong><br>
                                ${data.created_at}
                            </div>
                            <div>
                                <strong>IP Address:</strong><br>
                                ${data.ip_address || 'Not recorded'}
                            </div>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <strong>Description:</strong><br>
                            ${data.description || 'No description available'}
                        </div>
                        <div>
                            <strong>User Agent:</strong><br>
                            <small style="color: #6b7280;">${data.user_agent || 'Not recorded'}</small>
                        </div>
                    `;
                    document.getElementById('detailsModal').style.display = 'flex';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load details');
                });
        }

        function closeDetailsModal() {
            document.getElementById('detailsModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const passwordModal = document.getElementById('passwordModal');
            const detailsModal = document.getElementById('detailsModal');
            if (event.target === passwordModal) {
                closePasswordModal();
            }
            if (event.target === detailsModal) {
                closeDetailsModal();
            }
        }

        // Allow Enter key to submit password
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' && document.getElementById('passwordModal').style.display === 'flex') {
                verifyPassword();
            }
        });

        // Auto-hide success message
        setTimeout(function() {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 5000);

        function searchAllRecords() {
            const searchTerm = document.getElementById('searchInput').value.trim();
            
            if (!searchTerm) {
                alert('Please enter a search term');
                return;
            }
            
            // Set the global search input value
            document.getElementById('globalSearchInput').value = searchTerm;
            
            // Submit the form to perform global search
            document.getElementById('filtersForm').submit();
        }

        function verifyIP(ipAddress) {
            if (!ipAddress || ipAddress === 'Not recorded') {
                alert('No IP address recorded for this activity.');
                return;
            }
            
            // Show loading state
            const modal = document.getElementById('ipModal');
            const content = document.getElementById('ipContent');
            content.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div><p>Loading IP details...</p></div>';
            modal.style.display = 'flex';
            
            // Fetch IP details from ipapi.co
            fetch(`https://ipapi.co/${ipAddress}/json/`)
                .then(response => response.json())
                .then(data => {
                    content.innerHTML = `
                        <div class="grid grid-cols-2 gap-4">
                            <div><strong>IP Address:</strong><br>${ipAddress}</div>
                            <div><strong>Country:</strong><br>${data.country_name || 'Unknown'}</div>
                            <div><strong>Region:</strong><br>${data.region || 'Unknown'}</div>
                            <div><strong>City:</strong><br>${data.city || 'Unknown'}</div>
                            <div><strong>ISP:</strong><br>${data.org || 'Unknown'}</div>
                            <div><strong>Timezone:</strong><br>${data.timezone || 'Unknown'}</div>
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Error:', error);
                    content.innerHTML = '<div class="text-red-600">Failed to load IP details. Please try again.</div>';
                });
        }

        function closeIPModal() {
            document.getElementById('ipModal').style.display = 'none';
        }
    </script>
</x-admin-layout>