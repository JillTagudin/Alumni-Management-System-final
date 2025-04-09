<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Account Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Role</th>
                                <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td class="px-6 py-4 border-b border-gray-300">{{ $user->name }}</td>
                                    <td class="px-6 py-4 border-b border-gray-300">{{ $user->email }}</td>
                                    <td class="px-6 py-4 border-b border-gray-300">
                                        {{ $user->is_admin ? 'Admin' : 'User' }}
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-300">
                                        <form action="{{ route('AccountManagement.updateRole', $user) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_admin" value="{{ $user->is_admin ? '0' : '1' }}">
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                Make {{ $user->is_admin ? 'User' : 'Admin' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>