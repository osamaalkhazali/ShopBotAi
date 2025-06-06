<section>
    <header>
        <h2 class="text-lg font-medium app-section-title">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm app-muted-text">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="current_password" :value="__('Current Password')" class="app-label" />
            <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full app-input" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 auth-error" />
        </div>

        <div>
            <x-input-label for="password" :value="__('New Password')" class="app-label" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full app-input" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 auth-error" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="app-label" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full app-input" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 auth-error" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="app-btn-primary">{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm auth-success"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
