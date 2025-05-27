<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Chatbot Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .bg-gradient {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
        }

        .form-container {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(15px);
        }

        .animated-bg {
            background: linear-gradient(270deg, #047857, #059669, #10b981);
            background-size: 600% 600%;
            animation: gradientAnimation 8s ease infinite;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .error-shake {
            animation: shake 0.82s cubic-bezier(.36,.07,.19,.97) both;
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
        <!-- Logo and heading -->
        <div class="text-center">
            <div class="mx-auto h-20 w-20 rounded-full bg-emerald-600 flex items-center justify-center mb-4 shadow-lg">
                <i class="fas fa-robot text-white text-4xl"></i>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900">Welcome Back</h1>
            <p class="mt-2 text-gray-600">Sign in to your admin account</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl p-8 form-container">
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded error-shake" role="alert">
                    <div class="flex items-center">
                        <div class="py-1">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                        </div>
                        <div>
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <form class="space-y-6" method="POST" action="{{ route('admin.login.submit') }}">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="bg-gray-50 focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 py-3 border-gray-300 rounded-md"
                               placeholder="admin@example.com">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                               class="bg-gray-50 focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 py-3 border-gray-300 rounded-md"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-emerald-600 hover:text-emerald-700">Forgot password?</a>
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent
                           rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2
                           focus:ring-offset-2 focus:ring-emerald-500 animated-bg">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-emerald-300 group-hover:text-emerald-200"></i>
                        </span>
                        Sign in
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center text-sm text-gray-600 mt-6">
            <p>© {{ date('Y') }} Chatbot Admin. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
