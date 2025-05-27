<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">Verify Email</h2>
        <p class="text-gray-300 text-sm">Check your email for verification link</p>
    </div>

    <div class="mb-6 text-sm text-gray-300 bg-gray-800/30 p-4 rounded-lg border border-gray-700">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 auth-success">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <div class="auth-actions justify-center">
                <x-primary-button class="auth-btn-primary">
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <div class="auth-divider">or</div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <div class="auth-actions justify-center">
                <button type="submit" class="auth-btn-secondary">
                    {{ __('Log Out') }}
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
