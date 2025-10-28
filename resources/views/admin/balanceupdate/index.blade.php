<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Balance Update') }}
        </h2>
    </x-slot>

    <div class="min-h-screen relative">
        <!-- Search Section -->
        <div class="sticky top-16 z-20 bg-transparent backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-2">
                
                <!-- Search Section -->
                <div class="bg-white bg-opacity-80 backdrop-blur-sm shadow rounded-lg mb-4">
                    <div class="px-6 py-4">
                        <div class="flex flex-wrap gap-4">
                            <!-- Search Input -->
                            <div class="flex-1 min-w-64">
                                <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input type="text" id="searchInput" 
                                       placeholder="Search by student ID, name, or fee type..." 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <!-- Fee Type Filter -->
                            <div class="min-w-48">
                                <label for="feeTypeFilter" class="block text-sm font-medium text-gray-700 mb-1">Fee Type</label>
                                <select id="feeTypeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Fee Types</option>
                            <option value="Alumni Membership Fee">Alumni Membership Fee</option>
                        </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scrollable Content Area -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-2">

            <div class="p-6 bg-white shadow-md rounded-lg border border-gray-300 space-y-4">
        @if(isset($error))
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Connection Error
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>{{ $error }}</p>
                            <p class="mt-1">Please check your internet connection or contact the system administrator if the problem persists.</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Balance Update Table -->
        <div class="bg-white bg-opacity-80 backdrop-blur-sm shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Balance Update Records</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Manage payment history and balance updates.</p>
            </div>
            
            @if(isset($error))
                <div class="mx-4 mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $error }}
                </div>
            @endif
        
            @if(isset($history) && count($history) > 0)
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table id="balanceTable" class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Student ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fee Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount
                                </th>
                                <!-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th> -->
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($history as $payment)
                                <tr class="payment-row">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payment['student_id'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $payment['student_name'] ?? 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $payment['fee_type_name'] ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if(isset($payment['amount']))
                                            ₱{{ number_format(floatval($payment['amount']), 2) }}
                                        @else
                                            <span class="text-gray-400">Not set</span>
                                        @endif
                                    </td>
                                    <!-- <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-indigo-600 hover:text-indigo-900">
                                            View
                                        </button>
                                    </td> -->
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
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No payment records</h3>
                    <p class="mt-1 text-sm text-gray-500">No balance update history found.</p>
                </div>
            @endif
        </div>
            </div>
        </div>
    </div>

    <script>
        // Search and filter functionality for balance update table
        function filterTable() {
            let searchFilter = document.getElementById("searchInput").value.toLowerCase().trim().split(/\s+/);
            let feeTypeFilter = document.getElementById("feeTypeFilter").value.toLowerCase();
            let rows = document.querySelectorAll(".payment-row");

            rows.forEach(row => {
                let rowText = row.textContent.toLowerCase();
                let feeTypeCell = row.querySelector('td:nth-child(3)').textContent.toLowerCase(); // Fee type column

                // Check if all search keywords match
                let searchMatch = searchFilter.length === 0 || searchFilter.every(keyword => rowText.includes(keyword));
                
                // Check if fee type matches (if filter is selected)
                let feeTypeMatch = feeTypeFilter === "" || feeTypeCell.includes(feeTypeFilter);

                // Show row only if both conditions are met
                row.style.display = (searchMatch && feeTypeMatch) ? "" : "none";
            });
        }

        // Add event listeners
        document.getElementById("searchInput").addEventListener("keyup", filterTable);
        document.getElementById("feeTypeFilter").addEventListener("change", filterTable);
    </script>
</x-admin-layout>