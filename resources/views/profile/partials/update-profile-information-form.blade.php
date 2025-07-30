<section>
    <header>
        <h2 class="text-lg font-medium app-section-title">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm app-muted-text">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" class="app-label" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full app-input" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 auth-error" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="app-label" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full app-input" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2 auth-error" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 app-muted-text">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm app-link hover:app-link-hover rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm auth-success">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="app-btn-primary">{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
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
