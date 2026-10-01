<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hetzner Cloud') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Connect your Hetzner Cloud account') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Paste an API token generated for a Hetzner Cloud project under Security > API Tokens. The token is stored encrypted and only used to make requests on your behalf.') }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('hetzner.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('put')

                            <div>
                                <x-input-label for="hetzner_api_token" :value="__('Hetzner API Token')" />
                                <x-text-input id="hetzner_api_token" name="hetzner_api_token" type="password" class="mt-1 block w-full" placeholder="{{ $user->hasHetznerApiToken() ? __('•••••••••••••••••••• (connected)') : '' }}" autocomplete="off" />
                                <x-input-error class="mt-2" :messages="$errors->hetznerToken->get('hetzner_api_token')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Save') }}</x-primary-button>

                                @if (session('status') === 'hetzner-connected')
                                    <p class="text-sm text-gray-600">{{ __('Connected.') }}</p>
                                @elseif (session('status') === 'hetzner-disconnected')
                                    <p class="text-sm text-gray-600">{{ __('Disconnected.') }}</p>
                                @endif
                            </div>
                        </form>

                        @if ($user->hasHetznerApiToken())
                            <form method="post" action="{{ route('hetzner.destroy') }}" class="mt-6">
                                @csrf
                                @method('delete')
                                <x-secondary-button type="submit">{{ __('Disconnect') }}</x-secondary-button>
                            </form>

                            <p class="mt-4 text-sm">
                                <a href="{{ route('hetzner.servers') }}" class="underline text-gray-600 hover:text-gray-900">
                                    {{ __('View your servers') }}
                                </a>
                            </p>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
