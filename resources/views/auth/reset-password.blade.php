<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">Reset Password</h2>
        <p class="text-gray-300 text-sm">Enter your new password below</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="auth-form-section">
            <x-input-label for="email" :value="__('Email Address')" class="auth-label" />
            <x-text-input id="email" class="block mt-2 w-full auth-input" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="Email address" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 auth-error" />
        </div>

        <!-- Password -->
        <div class="auth-form-section">
            <x-input-label for="password" :value="__('New Password')" class="auth-label" />
            <x-text-input id="password" class="block mt-2 w-full auth-input" type="password" name="password" required autocomplete="new-password" placeholder="Enter new password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 auth-error" />
        </div>

        <!-- Confirm Password -->
        <div class="auth-form-section">
            <x-input-label for="password_confirmation" :value="__('Confirm New Password')" class="auth-label" />
            <x-text-input id="password_confirmation" class="block mt-2 w-full auth-input"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="Confirm new password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 auth-error" />
        </div>

        <div class="auth-actions justify-center">
            <x-primary-button class="auth-btn-primary">
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Back to Login -->
    <div class="auth-register-prompt">
        <p>Remember your password?</p>
        <a href="{{ route('login') }}" class="auth-btn-secondary">
            {{ __('Back to Sign In') }}
        </a>
    </div>
</x-guest-layout>
