<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">Create Account</h2>
        <p class="text-gray-300 text-sm">Join us and start your journey</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div class="auth-form-section">
            <x-input-label for="name" :value="__('Full Name')" class="auth-label" />
            <x-text-input id="name" class="block mt-2 w-full auth-input" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter your full name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 auth-error" />
        </div>

        <!-- Email Address -->
        <div class="auth-form-section">
            <x-input-label for="email" :value="__('Email Address')" class="auth-label" />
            <x-text-input id="email" class="block mt-2 w-full auth-input" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Enter your email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 auth-error" />
        </div>

        <!-- Password -->
        <div class="auth-form-section">
            <x-input-label for="password" :value="__('Password')" class="auth-label" />
            <x-text-input id="password" class="block mt-2 w-full auth-input"
                            type="password"
                            name="password"
                            required autocomplete="new-password"
                            placeholder="Create a password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 auth-error" />
        </div>

        <!-- Confirm Password -->
        <div class="auth-form-section">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="auth-label" />
            <x-text-input id="password_confirmation" class="block mt-2 w-full auth-input"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password"
                            placeholder="Confirm your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 auth-error" />
        </div>

        <!-- Actions -->
        <div class="auth-actions">
            <a class="auth-link" href="{{ route('login') }}">
                {{ __('Already have an account?') }}
            </a>

            <x-primary-button class="auth-btn-primary">
                {{ __('Create Account') }}
            </x-primary-button>
        </div>
    </form>


</x-guest-layout>

