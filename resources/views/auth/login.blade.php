<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-6 auth-success" :status="session('status')" />

    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">Welcome Back</h2>
        <p class="text-gray-300 text-sm">Sign in to your account to continue</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="auth-form-section">
            <x-input-label for="email" :value="__('Email Address')" class="auth-label" />
            <x-text-input id="email" class="block mt-2 w-full auth-input" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Enter your email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 auth-error" />
        </div>

        <!-- Password -->
        <div class="auth-form-section">
            <x-input-label for="password" :value="__('Password')" class="auth-label" />
            <x-text-input id="password" class="block mt-2 w-full auth-input"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 auth-error" />
        </div>

        <!-- Remember Me -->
        <div class="auth-form-section">
            <label for="remember_me" class="auth-remember">
                <input id="remember_me" type="checkbox" class="auth-checkbox" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>
        </div>

        <!-- Actions -->
        <div class="auth-actions">
            @if (Route::has('password.request'))
                <a class="auth-link" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif

            <x-primary-button class="auth-btn-primary">
                {{ __('Sign In') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Register Section -->
    <div class="auth-register-prompt">
        <p>Don't have an account yet?</p>
        <a href="{{ route('register') }}" class="auth-btn-secondary">
            {{ __('Create Account') }}
        </a>
    </div>
</x-guest-layout>
