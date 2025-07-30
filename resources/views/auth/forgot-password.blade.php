<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">Reset Password</h2>
        <p class="text-gray-300 text-sm">Enter your email to receive a reset link</p>
    </div>

    <div class="mb-6 text-sm text-gray-300 bg-gray-800/30 p-4 rounded-lg border border-gray-700">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6 auth-success" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="auth-form-section">
            <x-input-label for="email" :value="__('Email Address')" class="auth-label" />
            <x-text-input id="email" class="block mt-2 w-full auth-input" type="email" name="email" :value="old('email')" required autofocus placeholder="Enter your email address" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 auth-error" />
        </div>

        <div class="auth-actions justify-center">
            <x-primary-button class="auth-btn-primary">
                {{ __('Send Reset Link') }}
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
