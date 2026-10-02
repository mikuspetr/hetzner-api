<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $user->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-4">
                @if (session('status') === 'user-created')
                    <p class="text-sm text-green-600">{{ __('User created.') }}</p>
                @elseif (session('status') === 'user-updated')
                    <p class="text-sm text-green-600">{{ __('User updated.') }}</p>
                @endif

                <p><span class="font-medium">{{ __('Email') }}:</span> {{ $user->email }}</p>
                <p><span class="font-medium">{{ __('Email verified') }}:</span> {{ $user->email_verified_at ? __('Yes') : __('No') }}</p>
                <p><span class="font-medium">{{ __('Created') }}:</span> {{ $user->created_at->toDateTimeString() }}</p>

                <div class="flex items-center gap-4">
                    <a href="{{ route('users.edit', $user) }}" class="text-sm text-gray-600 underline hover:text-gray-900">{{ __('Edit') }}</a>
                    <a href="{{ route('users.index') }}" class="text-sm text-gray-600 underline hover:text-gray-900">{{ __('Back to users') }}</a>
                    @unless (auth()->user()->is($user))
                        <form method="post" action="{{ route('users.destroy', $user) }}">
                            @csrf
                            @method('delete')
                            <x-danger-button>{{ __('Delete user') }}</x-danger-button>
                        </form>
                    @endunless
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
