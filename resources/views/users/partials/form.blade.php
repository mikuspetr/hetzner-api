<form method="post" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" class="space-y-6">
    @csrf
    @if ($user->exists)
        @method('put')
    @endif

    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div>
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" :required="! $user->exists" autocomplete="new-password" />
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
        @if ($user->exists)
            <p class="mt-1 text-sm text-gray-600">{{ __('Leave blank to keep the current password.') }}</p>
        @endif
    </div>

    <div>
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" :required="! $user->exists" autocomplete="new-password" />
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ __('Save') }}</x-primary-button>
        <a href="{{ $user->exists ? route('users.show', $user) : route('users.index') }}" class="text-sm text-gray-600 underline hover:text-gray-900">{{ __('Cancel') }}</a>
    </div>
</form>
