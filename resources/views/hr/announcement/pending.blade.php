@php
    // Role protection at view level
    if (!Auth::user()->isHR()) {
        abort(403, 'Unauthorized access. HR role required.');
    }
@endphp

<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pending Announcements') }}
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
                        <h3 class="text-lg font-semibold">Your Pending Announcements</h3>
                        <a href="{{ route('hr.announcement.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                            Create New Announcement
                        </a>
                    </div>

                    @if($pendingAnnouncements->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="py-2 px-4 border-b text-left">Title</th>
                                        <th class="py-2 px-4 border-b text-left">Content Preview</th>
                                        <th class="py-2 px-4 border-b text-left">Status</th>
                                        <th class="py-2 px-4 border-b text-left">Created</th>
                                        <th class="py-2 px-4 border-b text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingAnnouncements as $announcement)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-2 px-4 border-b">
                                                <div class="font-medium text-gray-900">{{ $announcement->title }}</div>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <div class="text-sm text-gray-600">
                                                    {{ Str::limit(strip_tags($announcement->content), 80) }}
                                                </div>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    Pending Approval
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border-b text-sm text-gray-600">
                                                {{ $announcement->created_at->format('M d, Y g:i A') }}
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('hr.announcement.show', $announcement->id) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                                        View
                                                    </a>
                                                    @if($announcement->status === 'pending')
                                                        <a href="{{ route('hr.announcement.edit', $announcement->id) }}" class="text-green-600 hover:text-green-900 text-sm">
                                                            Edit
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $pendingAnnouncements->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No pending announcements</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new announcement.</p>
                            <div class="mt-6">
                                <a href="{{ route('hr.announcement.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                                    New Announcement
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>