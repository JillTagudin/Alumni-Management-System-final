<x-user-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Announcements') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Sample Announcements (Will be dynamic later) -->
                    <div class="space-y-8">
                        <!-- Announcement 1 -->
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Important Update
                                </h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                    Posted on March 15, 2024
                                </p>
                            </div>
                            <div class="border-t border-gray-200">
                                <div class="px-4 py-5 sm:p-6">
                                    <p class="text-gray-700">
                                        Sample announcement content will appear here. This is a placeholder that will be replaced with real announcements from the admin dashboard.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Announcement 2 -->
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    System Maintenance
                                </h3>
                                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                    Posted on March 10, 2024
                                </p>
                            </div>
                            <div class="border-t border-gray-200">
                                <div class="px-4 py-5 sm:p-6">
                                    <p class="text-gray-700">
                                        Another sample announcement. This layout will be used to display actual announcements from administrators.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-user-layout>