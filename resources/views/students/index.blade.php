<x-admin-layout>
    <div class="min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white">Students Management</h1>
                        <p class="mt-2 text-gray-200">Monitor and sync student records with alumni database</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="refreshData()" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center backdrop-blur-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white bg-opacity-80 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/20">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-2.239"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Students</p>
                            <p class="text-2xl font-bold text-gray-900" id="totalStudents">{{ count($students ?? []) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white bg-opacity-80 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/20">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Registered Alumni</p>
                            <p class="text-2xl font-bold text-gray-900" id="registeredCount">-</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white bg-opacity-80 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/20">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Unregistered</p>
                            <p class="text-2xl font-bold text-gray-900" id="unregisteredCount">-</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white bg-opacity-80 backdrop-blur-sm rounded-xl shadow-lg p-6 border border-white/20">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 00-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Sync Status</p>
                            <p class="text-sm font-medium text-gray-900" id="syncStatus">Last synced: 6:57:18 PM</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alumni Registration Sync Section -->
            <div class="bg-white bg-opacity-80 backdrop-blur-sm rounded-xl shadow-lg p-6 mb-8 border border-white/20">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Alumni Registration Sync</h3>
                        <p class="text-sm text-gray-600">Sync student records with alumni database to identify unregistered students and send them invitation to join our Alumni community</p>
                    </div>
                    <div class="flex space-x-3">
                        <button onclick="syncWithAlumni()" id="syncButton" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Sync with Alumni Records
                        </button>
                        <button onclick="sendRegistrationInvitations()" id="emailButton" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Send Registration Invitations
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filter and Search Section -->
            <div class="bg-white bg-opacity-80 backdrop-blur-sm rounded-xl shadow-lg p-6 mb-8 border border-white/20">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex space-x-2">
                        <button onclick="filterStudents('all')" id="filterAll" class="filter-btn active bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            All Students
                        </button>
                        <button onclick="filterStudents('registered')" id="filterRegistered" class="filter-btn bg-gray-200 text-gray-700 hover:bg-gray-300 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            Registered Only
                        </button>
                        <button onclick="filterStudents('unregistered')" id="filterUnregistered" class="filter-btn bg-gray-200 text-gray-700 hover:bg-gray-300 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            Unregistered Only
                        </button>
                    </div>
                    <div class="flex-1 max-w-md">
                        <input type="text" id="searchInput" placeholder="Search students..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Students Table -->
            <div class="bg-white bg-opacity-80 backdrop-blur-sm rounded-xl shadow-lg overflow-hidden border border-white/20">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 bg-opacity-80">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year/Section</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Level</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alumni Status</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody" class="bg-white bg-opacity-60 divide-y divide-gray-200">
                            @if(isset($students) && count($students) > 0)
                                @foreach($students as $student)
                                <tr class="student-row hover:bg-gray-50 hover:bg-opacity-80 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student['student_number'] ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student['name'] ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student['email'] ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student['program'] ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student['section'] ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student['year_level'] ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="alumni-status px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Unknown
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-2.239"></path>
                                            </svg>
                                            <p class="text-lg font-medium">No students available</p>
                                            <p class="text-sm">Click refresh to load student data from the API</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sync Results Modal -->
    <div id="syncModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Sync Results</h3>
                    <button onclick="closeSyncModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="syncResults" class="max-h-96 overflow-y-auto">
                    <!-- Results will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Email Invitation Modal -->
    <div id="emailModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center max-h-[90vh] overflow-y-auto">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Send Registration Invitations</h3>
                <button onclick="closeEmailModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="emailModalContent">
                <p class="text-gray-600 mb-4">Send registration invitations to unregistered students to encourage them to join the alumni network.</p>
                <div class="space-y-3">
                    <button onclick="sendEmailInvitations('unregistered')" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        Send to All Unregistered Students
                    </button>
                    <!-- Hidden: Send to Selected Students button -->
                    <!-- <button onclick="sendEmailInvitations('selected')" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        Send to Selected Students (Coming Soon)
                    </button> -->
                </div>
            </div>
        </div>
    </div>

    <!-- Email Results Modal -->
    <div id="emailResultsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center max-h-[90vh] overflow-y-auto">
        <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Email Invitation Results</h3>
                <button onclick="closeEmailResultsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="emailResultsContent">
                <!-- Results will be populated here -->
            </div>
        </div>
    </div>

    <script>
        let allStudents = [];
        let currentFilter = 'all';

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            refreshData();
            
            // Add search functionality
            document.getElementById('searchInput').addEventListener('input', function() {
                filterAndSearchStudents();
            });
        });

        function refreshData() {
            fetch('/students/fetch')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        allStudents = data.students;
                        updateStudentsTable(allStudents);
                        updateStatistics();
                    } else {
                        console.error('Failed to fetch students:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching students:', error);
                });
        }

        function syncWithAlumni() {
            const syncButton = document.getElementById('syncButton');
            const originalText = syncButton.innerHTML;
            
            // Show loading state
            syncButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Syncing...';
            syncButton.disabled = true;

            fetch('/students/sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update statistics
                    document.getElementById('registeredCount').textContent = data.registered_count;
                    document.getElementById('unregisteredCount').textContent = data.unregistered_count;
                    document.getElementById('syncStatus').textContent = 'Last synced: ' + new Date().toLocaleTimeString();
                    
                    // Update student statuses
                    updateStudentStatuses(data.unregistered_students);
                    
                    // Show results modal
                    showSyncResults(data);
                } else {
                    alert('Sync failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Sync error:', error);
                alert('Sync failed. Please try again.');
            })
            .finally(() => {
                // Restore button
                syncButton.innerHTML = originalText;
                syncButton.disabled = false;
            });
        }

        function updateStudentStatuses(unregisteredStudents) {
            const unregisteredNumbers = unregisteredStudents.map(s => s.student_number);
            
            document.querySelectorAll('.student-row').forEach(row => {
                const studentNumber = row.querySelector('td:first-child').textContent.trim();
                const statusBadge = row.querySelector('.alumni-status');
                
                if (unregisteredNumbers.includes(studentNumber)) {
                    statusBadge.className = 'alumni-status px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800';
                    statusBadge.textContent = 'Not Registered';
                    row.setAttribute('data-status', 'unregistered');
                } else {
                    statusBadge.className = 'alumni-status px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800';
                    statusBadge.textContent = 'Registered';
                    row.setAttribute('data-status', 'registered');
                }
            });
        }

        function showSyncResults(data) {
            const modal = document.getElementById('syncModal');
            const resultsDiv = document.getElementById('syncResults');
            
            let html = `
                <div class="mb-4">
                    <h4 class="font-medium text-gray-900 mb-2">Sync Summary</h4>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="bg-blue-50 p-3 rounded">
                            <div class="text-2xl font-bold text-blue-600">${data.total_students}</div>
                            <div class="text-sm text-blue-600">Total Students</div>
                        </div>
                        <div class="bg-green-50 p-3 rounded">
                            <div class="text-2xl font-bold text-green-600">${data.registered_count}</div>
                            <div class="text-sm text-green-600">Registered</div>
                        </div>
                        <div class="bg-red-50 p-3 rounded">
                            <div class="text-2xl font-bold text-red-600">${data.unregistered_count}</div>
                            <div class="text-sm text-red-600">Unregistered</div>
                        </div>
                    </div>
                </div>
            `;
            
            if (data.unregistered_students.length > 0) {
                html += `
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Unregistered Students</h4>
                        <div class="max-h-48 overflow-y-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Student Number</th>
                                        <th class="px-3 py-2 text-left">Name</th>
                                        <th class="px-3 py-2 text-left">Email</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                `;
                
                data.unregistered_students.forEach(student => {
                    html += `
                        <tr>
                            <td class="px-3 py-2">${student.student_number}</td>
                            <td class="px-3 py-2">${student.name}</td>
                            <td class="px-3 py-2">${student.email}</td>
                        </tr>
                    `;
                });
                
                html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            }
            
            resultsDiv.innerHTML = html;
            modal.classList.remove('hidden');
        }

        function closeSyncModal() {
            document.getElementById('syncModal').classList.add('hidden');
        }

        function filterStudents(type) {
            currentFilter = type;
            
            // Update button styles
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
            });
            
            let activeBtn;
            if (type === 'all') {
                activeBtn = document.getElementById('filterAll');
            } else if (type === 'registered') {
                activeBtn = document.getElementById('filterRegistered');
            } else if (type === 'unregistered') {
                activeBtn = document.getElementById('filterUnregistered');
            }
            
            if (activeBtn) {
                activeBtn.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
                activeBtn.classList.add('active', 'bg-blue-600', 'text-white');
            }
            
            filterAndSearchStudents();
        }

        function filterAndSearchStudents() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const rows = document.querySelectorAll('.student-row');
            
            rows.forEach(row => {
                const studentNumber = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const status = row.getAttribute('data-status') || '';
                
                const matchesSearch = studentNumber.includes(searchTerm) || 
                                    name.includes(searchTerm) || 
                                    email.includes(searchTerm);
                
                let matchesFilter = false;
                if (currentFilter === 'all') {
                    matchesFilter = true;
                } else if (currentFilter === 'registered') {
                    matchesFilter = status === 'registered';
                } else if (currentFilter === 'unregistered') {
                    matchesFilter = status === 'unregistered';
                }
                
                if (matchesSearch && matchesFilter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function updateStudentsTable(students) {
            // This function would update the table if we're dynamically loading students
            // For now, we'll just update the total count
            document.getElementById('totalStudents').textContent = students.length;
        }

        function updateStatistics() {
            // Update total students count
            const totalStudents = document.querySelectorAll('.student-row').length;
            document.getElementById('totalStudents').textContent = totalStudents;
        }

        // Email invitation functions
        function sendRegistrationInvitations() {
            document.getElementById('emailModal').classList.remove('hidden');
        }

        function closeEmailModal() {
            document.getElementById('emailModal').classList.add('hidden');
        }

        function closeEmailResultsModal() {
            document.getElementById('emailResultsModal').classList.add('hidden');
        }

        async function sendEmailInvitations(type) {
            const emailButton = document.getElementById('emailButton');
            const originalText = emailButton.innerHTML;
            
            // Show loading state
            emailButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sending Invitations...
            `;
            emailButton.disabled = true;
            
            closeEmailModal();
            
            try {
                const response = await fetch('{{ route("students.send-invitations") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        type: type
                    })
                });
                
                const result = await response.json();
                
                // Show results modal
                document.getElementById('emailResultsContent').innerHTML = `
                    <div class="space-y-4">
                        ${result.success ? 
                            `<div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-green-800">Success!</h3>
                                        <div class="mt-2 text-sm text-green-700">
                                            <p>${result.message}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>` :
                            `<div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Error</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <p>${result.message}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>`
                        }
                        
                        ${result.details ? `
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-blue-800 mb-2">Details:</h4>
                                <ul class="text-sm text-blue-700 space-y-1">
                                    <li>• Total unregistered students: ${result.details.total_unregistered || 0}</li>
                                    <li>• Emails sent successfully: ${result.details.success_count || 0}</li>
                                    <li>• Failed to send: ${result.details.failed_count || 0}</li>
                                </ul>
                            </div>
                        ` : ''}
                    </div>
                `;
                
                document.getElementById('emailResultsModal').classList.remove('hidden');
                
            } catch (error) {
                console.error('Error sending invitations:', error);
                alert('An error occurred while sending invitations. Please try again.');
            } finally {
                // Restore button state
                emailButton.innerHTML = originalText;
                emailButton.disabled = false;
            }
        }
    </script>
</x-admin-layout>