<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your Hetzner Cloud servers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                @if ($error)
                    <p class="text-sm text-red-600">{{ $error }}</p>
                @elseif (empty($servers))
                    <p class="text-sm text-gray-600">
                        {{ __('No servers were found, or you have not connected a Hetzner Cloud API token yet.') }}
                        <a href="{{ route('hetzner.edit') }}" class="underline">{{ __('Connect your account') }}</a>.
                    </p>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Name') }}</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('IPv4') }}</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Type') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($servers as $server)
                                <tr>
                                    <td class="px-3 py-2 text-sm text-gray-900">{{ $server['name'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-900">{{ $server['status'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-900">{{ $server['public_net']['ipv4']['ip'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-sm text-gray-900">{{ $server['server_type']['name'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
