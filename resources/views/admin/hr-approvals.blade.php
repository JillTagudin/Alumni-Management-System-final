@php
    // Role protection at view level
    if (!Auth::user()->hasAdminPrivileges()) {
        abort(403, 'Unauthorized access. Admin privileges required.');
    }
@endphp

<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('HR Announcement Approvals') }}
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

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">HR Announcements Pending Approval</h3>
                        <div class="text-sm text-gray-600">
                            {{ $hrPendingAnnouncements->count() }} pending approval{{ $hrPendingAnnouncements->count() !== 1 ? 's' : '' }}
                        </div>
                    </div>

                    @if($hrPendingAnnouncements->count() > 0)
                        <div class="space-y-6">
                            @foreach($hrPendingAnnouncements as $announcement)
                                <div class="border border-gray-200 rounded-lg p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex-1">
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $announcement->title }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">
                                                Created by: {{ $announcement->user->name }} (HR) • 
                                                {{ $announcement->created_at->format('M d, Y g:i A') }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                            Pending Approval
                                        </span>
                                    </div>

                                    <div class="mb-4">
                                        <h5 class="font-medium text-gray-900 mb-2">Content:</h5>
                                        <div class="bg-gray-50 p-4 rounded-lg">
                                            <div class="prose max-w-none">
                                                {!! $announcement->content !!}
                                            </div>
                                        </div>
                                    </div>

                                    @if($announcement->attachments)
                                        <div class="mb-4">
                                            <h5 class="font-medium text-gray-900 mb-2">Attachments:</h5>
                                            <div class="text-sm text-gray-600">
                                                {{ count(json_decode($announcement->attachments, true)) }} file(s) attached
                                            </div>
                                        </div>
                                    @endif

                                    <div class="flex justify-end space-x-3">
                                        <button onclick="showRejectModal({{ $announcement->id }})" 
                                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                            Reject
                                        </button>
                                        <button onclick="showApproveModal({{ $announcement->id }})" 
                                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                            Approve
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $hrPendingAnnouncements->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No pending HR announcements</h3>
                            <p class="mt-1 text-sm text-gray-500">All HR announcements have been reviewed.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-4">Approve Announcement</h3>
                <p class="text-sm text-gray-500 mt-2">Are you sure you want to approve this announcement? It will be published and visible to all users.</p>
                <form id="approveForm" method="POST">
                    @csrf
                    <div class="flex justify-center space-x-3 mt-6">
                        <button type="button" onclick="closeApproveModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Approve
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900">Reject Announcement</h3>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="mt-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 text-left">Reason for rejection:</label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="3" 
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                  placeholder="Please provide a reason for rejecting this announcement..." required></textarea>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeRejectModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showApproveModal(announcementId) {
            const modal = document.getElementById('approveModal');
            const form = document.getElementById('approveForm');
            form.action = `/admin/hr-announcement/${announcementId}/approve`;
            modal.classList.remove('hidden');
        }

        function closeApproveModal() {
            const modal = document.getElementById('approveModal');
            modal.classList.add('hidden');
        }

        function showRejectModal(announcementId) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = `/admin/hr-announcement/${announcementId}/reject`;
            modal.classList.remove('hidden');
        }

        function closeRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.add('hidden');
            document.getElementById('rejection_reason').value = '';
        }

        // Close modals when clicking outside
        document.getElementById('approveModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeApproveModal();
            }
        });

        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
    </script>
</x-admin-layout>