<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Approval Management') }}
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

                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-8">
                            <a href="{{ route('approval.index', ['tab' => 'pending']) }}" 
                               class="{{ (!isset($activeTab) || $activeTab === 'pending') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                Pending Changes
                                @if(isset($pendingChanges) && $pendingChanges->count() > 0)
                                    <span id="admin-pending-count" class="ml-2 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $pendingChanges->count() }}</span>
                                @else
                                    <span id="admin-pending-count" class="ml-2 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full" style="display: none;"></span>
                                @endif
                            </a>
                            <a href="{{ route('approval.index', ['tab' => 'user_approvals']) }}" 
                               class="{{ (isset($activeTab) && $activeTab === 'user_approvals') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                User Approvals
                                @if(isset($pendingUsers) && $pendingUsers->count() > 0)
                                    <span class="ml-2 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $pendingUsers->count() }}</span>
                                @endif
                            </a>
                            <a href="{{ route('approval.index', ['tab' => 'history']) }}" 
                               class="{{ (isset($activeTab) && $activeTab === 'history') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                My Approval History
                            </a>
                        </nav>
                    </div>

                    @if(!isset($activeTab) || $activeTab === 'pending')
                        <!-- Pending Changes Tab Content -->
                        @if($pendingChanges->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-300">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="py-2 px-4 border-b text-left">User</th>
                                            <th class="py-2 px-4 border-b text-left">Change Type</th>
                                            <th class="py-2 px-4 border-b text-left">Description</th>
                                            <th class="py-2 px-4 border-b text-left">Requested At</th>
                                            <th class="py-2 px-4 border-b text-left">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pending-changes-tbody">
                                        @foreach($pendingChanges as $change)
                                            <tr class="hover:bg-gray-50" id="change-row-{{ $change->id }}">
                                                <td class="py-2 px-4 border-b">
                                                    @if($change->staffUser)
                                                        {{ $change->staffUser->name }}
                                                        <br>
                                                        <small class="text-gray-500">{{ $change->staffUser->email }}</small>
                                                    @else
                                                        <span class="text-gray-500">Unknown User</span>
                                                    @endif
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ ucfirst(str_replace('_', ' ', $change->change_type)) }}
                                                    </span>
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    {{ $change->description }}
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    {{ $change->created_at->format('M d, Y g:i A') }}
                                                </td>
                                                <td class="py-2 px-4 border-b" id="actions-{{ $change->id }}">
                                                    <div class="flex space-x-2">
                                                        <!-- Approve Button -->
                                                        <button onclick="openApprovalModal({{ $change->id }}, 'approve')" 
                                                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm"
                                                                id="approve-btn-{{ $change->id }}">
                                                            Approve
                                                        </button>
                                                        
                                                        <!-- Deny Button -->
                                                        <button onclick="openApprovalModal({{ $change->id }}, 'deny')" 
                                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm"
                                                                style="background-color: #ef4444 !important; color: white !important;"
                                                                id="deny-btn-{{ $change->id }}">
                                                            Deny
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $pendingChanges->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div id="empty-state" class="text-center py-8">
                                <div class="text-gray-500 text-lg">
                                    <i class="fas fa-check-circle text-4xl mb-4"></i>
                                    <p>No pending changes to review.</p>
                                </div>
                            </div>
                        @endif
                    @elseif(isset($activeTab) && $activeTab === 'user_approvals')
                        <!-- User Approvals Tab Content -->
                        
                        <!-- Search and Filters Section -->
                        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                            <!-- Global Search -->
                            <div class="mb-4">
                                <form method="GET" action="{{ route('approval.index') }}" id="userSearchForm">
                                    <input type="hidden" name="tab" value="user_approvals">
                                    <input type="hidden" name="subtab" value="{{ $activeSubTab ?? 'pending' }}">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-1">
                                            <input type="text" 
                                                   name="global_search" 
                                                   value="{{ request('global_search') }}"
                                                   placeholder="Search users by name, email, student number, course, occupation, company..."
                                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                   id="globalSearchInput">
                                        </div>
                                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                                            Search All
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Filters Form -->
                            <form method="GET" action="{{ route('approval.index') }}" id="userFiltersForm">
                                <input type="hidden" name="tab" value="user_approvals">
                                <input type="hidden" name="subtab" value="{{ $activeSubTab ?? 'pending' }}">
                                <input type="hidden" name="global_search" value="{{ request('global_search') }}">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <!-- Role Filter -->
                                    <div>
                                        <label for="role_filter" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                                        <select name="role_filter" id="role_filter" class="w-full pl-3 pr-8 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none bg-white">
                                            <option value="all">All Roles</option>
                                            @if(isset($roleOptions))
                                                @foreach($roleOptions as $role)
                                                    <option value="{{ $role }}" {{ request('role_filter') == $role ? 'selected' : '' }}>
                                                        {{ $role }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <!-- Date From -->
                                    <div>
                                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <!-- Date To -->
                                    <div>
                                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <div class="flex justify-between items-center mt-4">
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Apply Filters
                                    </button>
                                    <a href="{{ route('approval.index', ['tab' => 'user_approvals', 'subtab' => $activeSubTab ?? 'pending']) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                        Clear All Filters
                                    </a>
                                </div>
                            </form>

                            <!-- Active Filters Display -->
                            @if(request()->hasAny(['global_search', 'role_filter', 'date_from', 'date_to']))
                                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-sm font-medium text-blue-800">Active Filters:</span>
                                        
                                        @if(request('global_search'))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Search: "{{ request('global_search') }}"
                                                <a href="{{ request()->fullUrlWithQuery(['global_search' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                                            </span>
                                        @endif
                                        
                                        @if(request('role_filter') && request('role_filter') !== 'all')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Role: {{ request('role_filter') }}
                                                <a href="{{ request()->fullUrlWithQuery(['role_filter' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                                            </span>
                                        @endif
                                        
                                        @if(request('date_from'))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                From: {{ request('date_from') }}
                                                <a href="{{ request()->fullUrlWithQuery(['date_from' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                                            </span>
                                        @endif
                                        
                                        @if(request('date_to'))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                To: {{ request('date_to') }}
                                                <a href="{{ request()->fullUrlWithQuery(['date_to' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Sub-tabs Navigation for User Approvals -->
                        <div class="mb-6">
                            <div class="border-b border-gray-200">
                                <nav class="-mb-px flex space-x-8">
                                    <!-- Pending Registrations Sub-tab -->
                                    <a href="{{ route('approval.index', array_merge(request()->query(), ['tab' => 'user_approvals', 'subtab' => 'pending'])) }}" 
                                       class="py-2 px-1 border-b-2 font-medium text-sm {{ (!isset($activeSubTab) || $activeSubTab === 'pending') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                        Pending Registrations
                                        @if(isset($pendingCount) && $pendingCount > 0)
                                            <span class="ml-2 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                {{ $pendingCount }}
                                            </span>
                                        @endif
                                    </a>
                                    
                                    <!-- Approved Users Sub-tab -->
                                    <a href="{{ route('approval.index', array_merge(request()->query(), ['tab' => 'user_approvals', 'subtab' => 'approved'])) }}" 
                                       class="py-2 px-1 border-b-2 font-medium text-sm {{ (isset($activeSubTab) && $activeSubTab === 'approved') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                        Approved Users
                                        @if(isset($approvedCount) && $approvedCount > 0)
                                            <span class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                {{ $approvedCount }}
                                            </span>
                                        @endif
                                    </a>
                                    
                                    <!-- Denied Users Sub-tab -->
                                    <a href="{{ route('approval.index', array_merge(request()->query(), ['tab' => 'user_approvals', 'subtab' => 'denied'])) }}" 
                                       class="py-2 px-1 border-b-2 font-medium text-sm {{ (isset($activeSubTab) && $activeSubTab === 'denied') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                        Denied Users
                                        @if(isset($deniedCount) && $deniedCount > 0)
                                            <span class="ml-2 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                {{ $deniedCount }}
                                            </span>
                                        @endif
                                    </a>
                                </nav>
                            </div>
                        </div>

                        @if(isset($pendingUsers) && $pendingUsers->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-300">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="py-2 px-4 border-b text-left">Name</th>
                                            <th class="py-2 px-4 border-b text-left">Email</th>
                                            <th class="py-2 px-4 border-b text-left">Role</th>
                                            <th class="py-2 px-4 border-b text-left">Student Number</th>
                                            <th class="py-2 px-4 border-b text-left">
                                                @if(isset($activeSubTab) && $activeSubTab === 'approved')
                                                    Approved At
                                                @elseif(isset($activeSubTab) && $activeSubTab === 'denied')
                                                    Denied At
                                                @else
                                                    Registered At
                                                @endif
                                            </th>
                                            @if(!isset($activeSubTab) || $activeSubTab === 'pending')
                                                <th class="py-2 px-4 border-b text-left">Actions</th>
                                            @else
                                                <th class="py-2 px-4 border-b text-left">Status</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingUsers as $user)
                                            <tr class="hover:bg-gray-50">
                                                <td class="py-2 px-4 border-b">
                                                    {{ $user->name }}
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    {{ $user->email }}
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $user->role }}
                                                    </span>
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    {{ $user->student_number ?? 'N/A' }}
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    @if(isset($activeSubTab) && $activeSubTab === 'approved' && isset($user->approved_at))
                                                        {{ \Carbon\Carbon::parse($user->approved_at)->format('M d, Y H:i') }}
                                                    @elseif(isset($activeSubTab) && $activeSubTab === 'denied')
                                                        {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y H:i') }}
                                                    @else
                                                        {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y H:i') }}
                                                    @endif
                                                </td>
                                                @if(!isset($activeSubTab) || $activeSubTab === 'pending')
                                                    <td class="py-2 px-4 border-b">
                                                        <div class="flex space-x-2">
                                                            <button onclick="openUserApprovalModal({{ $user->id }}, 'approve')" 
                                                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                                Approve
                                                            </button>
                                                            <button onclick="openUserApprovalModal({{ $user->id }}, 'deny')" 
                                                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                                Deny
                                                            </button>
                                                        </div>
                                                    </td>
                                                @else
                                                    <td class="py-2 px-4 border-b">
                                                        @if($user->approval_status === 'approved')
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                ✓ Approved
                                                            </span>
                                                        @elseif($user->approval_status === 'denied')
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                ✗ Denied
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                ⏳ Pending
                                                            </span>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $pendingUsers->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="text-center py-8">
                                @if(request()->hasAny(['global_search', 'role_filter', 'date_from', 'date_to']))
                                    <div class="text-gray-500 text-lg">
                                        <i class="fas fa-search text-4xl mb-4"></i>
                                        <p>No users found matching your search criteria.</p>
                                        <p class="text-sm mt-2">Try adjusting your filters or search terms.</p>
                                        <a href="{{ route('approval.index', ['tab' => 'user_approvals', 'subtab' => $activeSubTab ?? 'pending']) }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                            Clear All Filters
                                        </a>
                                    </div>
                                @else
                                    <div class="text-gray-500 text-lg">
                                        @if(!isset($activeSubTab) || $activeSubTab === 'pending')
                                            <i class="fas fa-user-clock text-4xl mb-4"></i>
                                            <p>No pending user registrations</p>
                                            <p class="text-gray-400 mt-2">All user registrations have been processed.</p>
                                        @elseif($activeSubTab === 'approved')
                                            <i class="fas fa-user-check text-4xl mb-4"></i>
                                            <p>No approved users found</p>
                                            <p class="text-gray-400 mt-2">Users you approve will appear here.</p>
                                        @elseif($activeSubTab === 'denied')
                                            <i class="fas fa-user-times text-4xl mb-4"></i>
                                            <p>No denied users found</p>
                                            <p class="text-gray-400 mt-2">Users you deny will appear here.</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    @else
                        <!-- Approval History Tab Content -->
                        @if(isset($approvalHistory) && $approvalHistory->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-300">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="py-2 px-4 border-b text-left">User</th>
                                            <th class="py-2 px-4 border-b text-left">Change Type</th>
                                            <th class="py-2 px-4 border-b text-left">Description</th>
                                            <th class="py-2 px-4 border-b text-left">Status</th>
                                            <th class="py-2 px-4 border-b text-left">Requested At</th>
                                            <th class="py-2 px-4 border-b text-left">Reviewed At</th>
                                            <th class="py-2 px-4 border-b text-left">Review Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($approvalHistory as $change)
                                            <tr class="hover:bg-gray-50">
                                                <td class="py-2 px-4 border-b">
                                                    @if($change->staff_user)
                                                        {{ $change->staff_user->name }}
                                                        <br>
                                                        <small class="text-gray-500">{{ $change->staff_user->email }}</small>
                                                    @elseif($change->staffUser)
                                                        {{ $change->staffUser->name }}
                                                        <br>
                                                        <small class="text-gray-500">{{ $change->staffUser->email }}</small>
                                                    @else
                                                        <span class="text-gray-500">Unknown User</span>
                                                        <br>
                                                        <small class="text-gray-400">User ID: {{ $change->staff_user_id ?? 'N/A' }}</small>
                                                    @endif
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ ucfirst(str_replace('_', ' ', $change->change_type)) }}
                                                    </span>
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    {{ $change->description }}
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    @if($change->status === 'approved')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            ✓ Approved
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            ✗ Denied
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    {{ $change->created_at->format('M d, Y g:i A') }}
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    @if($change->reviewed_at)
                                                        @if(is_string($change->reviewed_at))
                                                            {{ \Carbon\Carbon::parse($change->reviewed_at)->format('M d, Y g:i A') }}
                                                        @else
                                                            {{ $change->reviewed_at->format('M d, Y g:i A') }}
                                                        @endif
                                                    @else
                                                        N/A
                                                    @endif
                                                    <br>
                                                    <small class="text-gray-500">by {{ ($change->reviewedBy ?? $change->reviewed_by) ? ($change->reviewedBy ?? $change->reviewed_by)->name : 'Unknown Admin' }}</small>
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    {{ $change->review_notes ?: 'No notes provided' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $approvalHistory->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="text-gray-500 text-lg">
                                    <i class="fas fa-history text-4xl mb-4"></i>
                                    <p>No approval history found.</p>
                                    <p class="text-sm mt-2">Changes you approve or deny will appear here.</p>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(!isset($activeTab) || $activeTab === 'pending')
        <!-- Approval Modal (only show for pending changes tab) -->
        <div id="approvalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden flex items-center justify-center">
            <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Confirm Action</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" id="modalMessage">Are you sure you want to perform this action?</p>
                        
                        <form id="approvalForm" method="POST">
                            @csrf
                            <div class="mt-4">
                                <label for="review_notes" class="block text-sm font-medium text-gray-700">Review Notes (Optional)</label>
                                <textarea id="review_notes" name="review_notes" rows="3" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Add any notes about your decision..."></textarea>
                            </div>
                            
                            <div class="flex justify-center space-x-4 mt-6">
                                <button type="button" onclick="closeApprovalModal()" 
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Cancel
                                </button>
                                <button type="submit" id="confirmButton" 
                                        class="font-bold py-2 px-4 rounded">
                                    Confirm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(isset($activeTab) && $activeTab === 'user_approvals')
        <!-- User Approval Modal -->
        <div id="userApprovalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden flex items-center justify-center">
            <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-medium text-gray-900" id="userModalTitle">Confirm Action</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" id="userModalMessage">Are you sure you want to perform this action?</p>
                        
                        <form id="userApprovalForm" method="POST">
                            @csrf
                            <div class="mt-4">
                                <label for="user_approval_notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                                <textarea id="user_approval_notes" name="approval_notes" rows="3" 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Add any notes about your decision..."></textarea>
                            </div>
                            
                            <div class="flex justify-center space-x-4 mt-6">
                                <button type="button" onclick="closeUserApprovalModal()" 
                                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Cancel
                                </button>
                                <button type="submit" id="userConfirmButton" 
                                        class="font-bold py-2 px-4 rounded">
                                    Confirm
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(!isset($activeTab) || $activeTab === 'pending')

        <script>
            let currentChangeId = null;
            let currentAction = null;
            
            function openApprovalModal(changeId, action) {
                // Persistent debugging with group
                console.group('🎯 Opening Approval Modal');
                console.log('Received changeId:', changeId);
                console.log('Type of changeId:', typeof changeId);
                console.log('Received action:', action);
                console.log('Is changeId truthy?', !!changeId);
                console.log('Is changeId null?', changeId === null);
                console.log('Is changeId undefined?', changeId === undefined);
                
                const modal = document.getElementById('approvalModal');
                const title = document.getElementById('modalTitle');
                const message = document.getElementById('modalMessage');
                const confirmButton = document.getElementById('confirmButton');
                
                currentChangeId = changeId;
                currentAction = action;
                
                console.log('Set currentChangeId to:', currentChangeId);
                console.log('Set currentAction to:', currentAction);
                console.groupEnd();
                
                if (action === 'approve') {
                    title.textContent = 'Approve Change';
                    message.textContent = 'Are you sure you want to approve this change?';
                    confirmButton.textContent = 'Approve';
                    confirmButton.className = 'bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded';
                } else {
                    title.textContent = 'Deny Change';
                    message.textContent = 'Are you sure you want to deny this change?';
                    confirmButton.textContent = 'Deny';
                    confirmButton.className = 'bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded';
                    confirmButton.style.backgroundColor = '#ef4444';
                    confirmButton.style.color = 'white';
                }
                
                modal.classList.remove('hidden');
            }
            
            function closeApprovalModal() {
                const modal = document.getElementById('approvalModal');
                modal.classList.add('hidden');
                document.getElementById('review_notes').value = '';
                currentChangeId = null;
                currentAction = null;
            }
            
            function showLoadingState(changeId) {
                const actionsCell = document.getElementById(`actions-${changeId}`);
                actionsCell.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                        <span class="text-sm text-gray-600">Processing...</span>
                    </div>
                `;
            }
            
            function updateRowAfterAction(changeId, action, reviewNotes) {
                const row = document.getElementById(`change-row-${changeId}`);
                const actionsCell = document.getElementById(`actions-${changeId}`);
                
                // Update the actions cell with status
                if (action === 'approve') {
                    actionsCell.innerHTML = `
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✓ Approved
                        </span>
                    `;
                    row.classList.add('bg-green-50');
                } else {
                    actionsCell.innerHTML = `
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            ✗ Denied
                        </span>
                    `;
                    row.classList.add('bg-red-50');
                }
                
                // Add fade out animation and remove row after delay
                setTimeout(() => {
                    row.style.transition = 'opacity 0.5s ease-out';
                    row.style.opacity = '0.5';
                    
                    setTimeout(() => {
                        row.remove();
                        
                        // Check if table is empty and show empty state
                        const tbody = row.parentElement;
                        if (tbody && tbody.children.length === 0) {
                            const tableContainer = tbody.closest('.overflow-x-auto');
                            if (tableContainer) {
                                tableContainer.innerHTML = `
                                    <div class="text-center py-8">
                                        <div class="text-gray-500 text-lg">
                                            <i class="fas fa-check-circle text-4xl mb-4"></i>
                                            <p>No pending changes to review.</p>
                                        </div>
                                    </div>
                                `;
                            }
                        }
                    }, 500);
                }, 2000);
            }
            
            function showNotification(message, type = 'success') {
                // Use centralized notification system if available
                if (window.notificationCenter) {
                    window.notificationCenter.show(message, type, 5000, false, true);
                    return;
                }
                
                // Fallback to local notification system
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-md shadow-lg ${
                    type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'
                }`;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Remove notification after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
            
            // Handle form submission with AJAX
            document.getElementById('approvalForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Store values BEFORE closing modal to prevent them from being cleared
                const changeId = currentChangeId;
                const action = currentAction;
                
                // Add persistent logging
                console.group('🔍 Approval Process Debug');
                console.log('Current Change ID:', currentChangeId);
                console.log('Current Action:', currentAction);
                console.log('Stored Change ID:', changeId);
                console.log('Stored Action:', action);
                console.log('Type of Change ID:', typeof changeId);
                
                if (!changeId || !action) {
                    console.error('❌ Missing changeId or action:', { changeId, action });
                    alert('Error: Missing change ID or action. Please refresh the page and try again.');
                    console.groupEnd();
                    return;
                }
                
                const reviewNotes = document.getElementById('review_notes').value;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                console.log('Review Notes:', reviewNotes);
                console.log('CSRF Token:', csrfToken ? 'Present' : 'Missing');
                
                // Show loading state and close modal AFTER storing values
                showLoadingState(changeId);
                closeApprovalModal();
                
                // Make AJAX request using stored values
                const actionUrl = action === 'approve' ? 
                    `/approval/${changeId}/approve` : 
                    `/approval/${changeId}/deny`;
                
                console.log('🌐 Making request to:', actionUrl);
                
                fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        review_notes: reviewNotes
                    })
                })
                .then(response => {
                    console.log('📡 Response received:');
                    console.log('  Status:', response.status);
                    console.log('  Status Text:', response.statusText);
                    console.log('  OK:', response.ok);
                    
                    // Check if response is not ok
                    if (!response.ok) {
                        console.error('❌ HTTP Error:', response.status, response.statusText);
                        // SHOW SERVER ERROR IN ALERT BEFORE PAGE REFRESHES
                        alert(`SERVER ERROR:\nStatus: ${response.status}\nStatus Text: ${response.statusText}\nURL: /approval/2/approve`);
                    }
                    
                    return response.text().then(text => {
                        console.log('📄 Raw response text:', text);
                        // SHOW RAW RESPONSE IN ALERT
                        if (!response.ok) {
                            alert(`RAW SERVER RESPONSE:\n${text.substring(0, 500)}...`);
                        }
                        try {
                            const parsed = JSON.parse(text);
                            console.log('✅ Parsed JSON:', parsed);
                            return parsed;
                        } catch (e) {
                            console.error('❌ JSON Parse Error:', e);
                            console.error('❌ Response was not valid JSON:', text);
                            
                            // SHOW JSON PARSE ERROR IN ALERT
                            alert(`JSON PARSE ERROR:\n${e.message}\n\nResponse was:\n${text.substring(0, 300)}...`);
                            
                            // Show user-friendly error
                            showNotification('Error: Invalid server response. Please check console for details.', 'error');
                            throw new Error('Invalid JSON response: ' + text);
                        }
                    });
                })
                .then(data => {
                    console.log('🎯 Processing response data:', data);
                    
                    if (data.success) {
                        console.log('✅ Success! Updating UI...');
                        updateRowAfterAction(changeId, action);
                        showNotification(data.message || `Change ${action}d successfully!`, 'success');
                        
                        // Update pending count in real-time
                        updateAdminPendingCount();
                    } else {
                        console.error('❌ Server returned error:', data.message);
                        // SHOW SERVER ERROR IN ALERT
                        alert(`SERVER ERROR:\n${data.message || 'Unknown server error'}`);
                        showNotification(data.message || 'An error occurred while processing the request.', 'error');
                        
                        // Restore the original buttons
                        const actionsCell = document.getElementById(`actions-${changeId}`);
                        if (actionsCell) {
                            location.reload(); // Reload to restore original state
                        }
                    }
                    console.groupEnd();
                })
                .catch(error => {
                    console.group('💥 CATCH ERROR');
                    console.error('❌ Fetch Error:', error);
                    console.error('❌ Error message:', error.message);
                    console.error('❌ Error stack:', error.stack);
                    console.groupEnd();
                    
                    // SHOW CATCH ERROR IN ALERT
                    alert(`NETWORK/FETCH ERROR:\n${error.message}\n\nStack:\n${error.stack}`);
                    
                    // Show persistent error message
                    showNotification('Network error: ' + error.message, 'error');
                    
                    // Restore the original buttons
                    const actionsCell = document.getElementById(`actions-${changeId}`);
                    if (actionsCell) {
                        location.reload(); // Reload to restore original state
                    }
                });
            });
            
            // Close modal when clicking outside
            document.getElementById('approvalModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeApprovalModal();
                }
            });
            
            // Real-time updates for admin approval interface
            let lastKnownCount = {{ isset($pendingChanges) ? $pendingChanges->count() : 0 }};
            let isOnline = navigator.onLine;
            let updateInterval;
            
            function updateAdminPendingCount() {
                if (!isOnline || document.hidden) return;
                
                fetch('/api/admin-pending-count', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const countBadge = document.getElementById('admin-pending-count');
                        const currentCount = data.count;
                        
                        if (countBadge) {
                            if (currentCount > 0) {
                                countBadge.textContent = currentCount;
                                countBadge.style.display = 'inline-flex';
                            } else {
                                countBadge.style.display = 'none';
                            }
                        }
                        
                        // Check for new submissions
                        if (currentCount > lastKnownCount) {
                            showNotification(`${currentCount - lastKnownCount} new submission(s) received!`, 'info');
                            
                            // If we're on the pending tab and the table is visible, refresh it
                            const pendingTab = new URLSearchParams(window.location.search).get('tab');
                            if (!pendingTab || pendingTab === 'pending') {
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            }
                        }
                        
                        lastKnownCount = currentCount;
                    }
                })
                .catch(error => {
                    console.error('Error fetching admin pending count:', error);
                });
            }
            
            function showNotification(message, type = 'success') {
                // Use centralized notification system if available
                if (window.notificationCenter) {
                    window.notificationCenter.show(message, type, 5000, false, true);
                    return;
                }
                
                // Fallback to local notification system
                const notification = document.createElement('div');
                let bgClass, borderClass, textClass;
                
                switch(type) {
                    case 'info':
                        bgClass = 'bg-blue-100';
                        borderClass = 'border-blue-400';
                        textClass = 'text-blue-700';
                        break;
                    case 'error':
                        bgClass = 'bg-red-100';
                        borderClass = 'border-red-400';
                        textClass = 'text-red-700';
                        break;
                    default:
                        bgClass = 'bg-green-100';
                        borderClass = 'border-green-400';
                        textClass = 'text-green-700';
                }
                
                notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-md shadow-lg ${bgClass} border ${borderClass} ${textClass}`;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Remove notification after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
            
            // Start real-time updates
            function startRealTimeUpdates() {
                updateAdminPendingCount(); // Initial update
                updateInterval = setInterval(updateAdminPendingCount, 5000); // Update every 5 seconds
            }
            
            function stopRealTimeUpdates() {
                if (updateInterval) {
                    clearInterval(updateInterval);
                }
            }
            
            // Handle online/offline status
            window.addEventListener('online', () => {
                isOnline = true;
                startRealTimeUpdates();
            });
            
            window.addEventListener('offline', () => {
                isOnline = false;
                stopRealTimeUpdates();
            });
            
            // Handle page visibility
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    stopRealTimeUpdates();
                } else {
                    startRealTimeUpdates();
                }
            });
            
            // Start updates when page loads
            if (isOnline) {
                startRealTimeUpdates();
            }
            
            // Cleanup on page unload
            window.addEventListener('beforeunload', stopRealTimeUpdates);
        </script>
    @endif

    @if(isset($activeTab) && $activeTab === 'user_approvals')
        <script>
            let currentUserId = null;
            let currentUserAction = null;
            
            function openUserApprovalModal(userId, action) {
                currentUserId = userId;
                currentUserAction = action;
                
                const modal = document.getElementById('userApprovalModal');
                const title = document.getElementById('userModalTitle');
                const message = document.getElementById('userModalMessage');
                const confirmButton = document.getElementById('userConfirmButton');
                const form = document.getElementById('userApprovalForm');
                
                if (action === 'approve') {
                    title.textContent = 'Approve User Registration';
                    message.textContent = 'Are you sure you want to approve this user registration?';
                    confirmButton.textContent = 'Approve';
                    confirmButton.className = 'bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded';
                    form.action = `/approval/users/${userId}/approve`;
                } else {
                    title.textContent = 'Deny User Registration';
                    message.textContent = 'Are you sure you want to deny this user registration?';
                    confirmButton.textContent = 'Deny';
                    confirmButton.className = 'bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded';
                    form.action = `/approval/users/${userId}/deny`;
                }
                
                modal.classList.remove('hidden');
            }
            
            function closeUserApprovalModal() {
                const modal = document.getElementById('userApprovalModal');
                modal.classList.add('hidden');
                
                // Clear form
                document.getElementById('user_approval_notes').value = '';
                currentUserId = null;
                currentUserAction = null;
            }
            
            // Close modal when clicking outside
            document.getElementById('userApprovalModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeUserApprovalModal();
                }
            });

            // Search functionality
            function searchAllUsers() {
                document.getElementById('userSearchForm').submit();
            }

            // Real-time search on Enter key
            document.getElementById('globalSearchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchAllUsers();
                }
            });

            // Auto-submit filters when changed
            document.getElementById('role_filter').addEventListener('change', function() {
                document.getElementById('userFiltersForm').submit();
            });

            document.getElementById('status_filter').addEventListener('change', function() {
                document.getElementById('userFiltersForm').submit();
            });

            document.getElementById('date_from').addEventListener('change', function() {
                document.getElementById('userFiltersForm').submit();
            });

            document.getElementById('date_to').addEventListener('change', function() {
                document.getElementById('userFiltersForm').submit();
            });
        </script>
    @endif
</x-admin-layout>