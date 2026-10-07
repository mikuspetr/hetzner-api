<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Users') }}</h2>
            <a href="{{ route('users.create') }}" class="text-sm text-gray-600 underline hover:text-gray-900">{{ __('Create user') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('status') === 'user-deleted')
                    <p class="mb-4 text-sm text-green-600">{{ __('User deleted.') }}</p>
                @endif

                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Name') }}</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Email') }}</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-3 py-2 text-sm text-gray-900">{{ $user->name }}</td>
                                <td class="px-3 py-2 text-sm text-gray-900">{{ $user->email }}</td>
                                <td class="px-3 py-2 text-sm">
                                    <a href="{{ route('users.show', $user) }}" class="text-gray-600 underline hover:text-gray-900">{{ __('View') }}</a>
                                    <a href="{{ route('users.edit', $user) }}" class="ms-3 text-gray-600 underline hover:text-gray-900">{{ __('Edit') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-2 text-sm text-gray-600">{{ __('No users found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
