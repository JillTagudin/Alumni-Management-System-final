<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Concern Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center mb-6">
                        <a href="{{ route('admin.alumni-concerns.index') }}" 
                           class="text-blue-600 hover:text-blue-800 mr-4">
                            ← Back to Concerns
                        </a>
                    </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Concern Details -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">{{ $concern->title }}</h2>
                                <p class="text-sm text-gray-600 mt-1">Submitted on {{ $concern->created_at->format('F d, Y \\a\\t g:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $concern->status_color }}">
                                    {{ $concern->status_label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category</label>
                                <p class="text-sm text-gray-900">{{ $concern->category_label }}</p>
                            </div>
                            {{-- Priority field hidden
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Priority</label>
                                <p class="text-sm {{ $concern->priority_color }} font-medium">{{ $concern->priority_label }}</p>
                            </div>
                            --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">User</label>
                                <p class="text-sm text-gray-900">{{ $concern->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $concern->user->email }}</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <div class="bg-gray-50 p-4 rounded-md">
                                <p class="text-gray-800 whitespace-pre-wrap">{{ $concern->description }}</p>
                            </div>
                        </div>

                        @if($concern->admin_response)
                            <div class="border-t pt-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Current Response</h3>
                                <div class="bg-blue-50 p-4 rounded-md">
                                    <p class="text-gray-800 whitespace-pre-wrap">{{ $concern->admin_response }}</p>
                                    @if($concern->responder)
                                        <div class="mt-3 text-sm text-gray-600">
                                            <p>Responded by: {{ $concern->responder->name }}</p>
                                            <p>Response date: {{ $concern->responded_at->format('F d, Y \\a\\t g:i A') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Response Form -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-md rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        {{ $concern->admin_response ? 'Update Response' : 'Respond to Concern' }}
                    </h3>

                    <form action="{{ route('admin.alumni-concerns.respond', $concern) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select id="status" 
                                    name="status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror"
                                    required>
                                @foreach(App\Models\AlumniConcern::STATUSES as $key => $label)
                                    <option value="{{ $key }}" {{ $concern->status == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="admin_response" class="block text-sm font-medium text-gray-700 mb-2">Response</label>
                            <textarea id="admin_response" 
                                      name="admin_response" 
                                      rows="6"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('admin_response') border-red-500 @enderror"
                                      placeholder="Type your response to the alumni..."
                                      required>{{ old('admin_response', $concern->admin_response) }}</textarea>
                            @error('admin_response')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" 
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ $concern->admin_response ? 'Update Response' : 'Send Response' }}
                        </button>
                    </form>

                    @if($concern->status !== 'closed')
                        <div class="mt-4 pt-4 border-t">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Quick Status Update</h4>
                            <form action="{{ route('admin.alumni-concerns.update-status', $concern) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="flex space-x-2">
                                    @if($concern->status === 'pending')
                                        <button type="submit" name="status" value="in_progress" 
                                                class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white text-xs py-1 px-2 rounded">
                                            Mark In Progress
                                        </button>
                                    @endif
                                    @if(in_array($concern->status, ['pending', 'in_progress']))
                                        <button type="submit" name="status" value="resolved" 
                                                class="flex-1 bg-green-600 hover:bg-green-700 text-white text-xs py-1 px-2 rounded">
                                            Mark Resolved
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>