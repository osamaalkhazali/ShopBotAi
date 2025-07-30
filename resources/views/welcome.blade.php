<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $siteSettings->site_name }} - AI Shopping Assistant</title>

        <!-- Favicon with cache busting -->
        <link rel="icon" href="{{ $siteSettings->getFaviconUrl() }}?v={{ time() }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ $siteSettings->getFaviconUrl() }}?v={{ time() }}" type="image/x-icon">

        <!-- Prevent favicon caching -->
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- CSS Variables -->
        <style>
            @include('layouts.css.root')
        </style>

        <!-- Styles / Scripts -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        @vite(['resources/css/app.css'])
        @vite(['resources/js/app.js'])
    </head>
    <body class="font-sans antialiased welcome-page">
        <div class="relative min-h-screen">
            <!-- Header Navigation -->
            <header class="relative z-10 py-6 px-6 md:px-10">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="flex items-center">
                        @if($siteSettings->site_logo)
                            <img src="{{ Storage::url($siteSettings->site_logo) }}" alt="{{ $siteSettings->site_name }}" class="h-10 w-auto">
                        @else
                            <svg class="h-10 w-10 primary-text" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        @endif
                        <span class="ml-3 text-2xl font-bold welcome-card-title">{{ $siteSettings->site_name }}</span>
                    </div>

                    <nav class="hidden md:flex items-center space-x-6">
                        <a href="#features" class="welcome-nav-link transition">Features</a>
                        <a href="#how-it-works" class="welcome-nav-link transition">How It Works</a>
                        <a href="#examples" class="welcome-nav-link transition">Examples</a>
                    </nav>

                    <div class="flex items-center space-x-4">
                        @if (Route::has('login'))
                            <div class="flex space-x-2">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="rounded-lg px-4 py-2 welcome-button-primary focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                                        <i class="fas fa-lightbulb mr-1"></i> My Recommendations
                                    </a>
                                    <a href="{{ url('/chatbot') }}" class="rounded-lg px-4 py-2 bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                                        <i class="fas fa-robot mr-1"></i> Try ChatBot
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 border welcome-button-secondary focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                                        Log in
                                    </a>

                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="rounded-lg px-4 py-2 welcome-button-primary focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                                            Register
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </header>

            <!-- Hero Section -->
            <main>
                <section class="py-20 px-6 md:px-10">
                    <div class="max-w-7xl mx-auto">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                            <div>
                                <h1 class="text-4xl md:text-5xl font-bold welcome-card-title">Find the Perfect Product with AI-Powered Shopping Assistant</h1>
                                <p class="mt-6 text-xl welcome-card-description">Ask our AI chatbot any shopping question, from gift ideas to outfits, and get personalized recommendations instantly.</p>
                                <div class="mt-10">
                                    <a href="{{ Route::has('register') ? route('register') : '#' }}" class="rounded-lg px-6 py-3 welcome-button-primary text-center font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">Get Started</a>
                                </div>
                            </div>
                            <div class="relative h-[30rem] rounded-xl overflow-hidden shadow-xl">
                                <!-- Chatbot Interface Preview in Hero Section -->
                                <div class="absolute inset-0 welcome-card rounded-xl flex flex-col">
                                    <div class="welcome-chatbot-header p-4 text-white flex items-center">
                                        <svg class="h-6 w-6 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                        <span class="font-medium">ShopBot Assistant</span>
                                    </div>
                                    <div class="flex-1 p-4 overflow-y-auto space-y-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            </div>
                                            <div class="ml-3 bg-gray-100 dark:bg-gray-800 rounded-lg px-4 py-2 max-w-md">
                                                <p class="welcome-demo-text">I need a gift for my chef friend</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full primary-bg flex items-center justify-center text-white">
                                                    <i class="fas fa-robot"></i>
                                                </div>
                                            </div>
                                            <div class="ml-3 welcome-chatbot-message rounded-lg px-4 py-2 max-w-md">
                                                <p class="welcome-demo-text">Here are some gift ideas for your chef friend:</p>

                                                <!-- Product grid in hero section with 16:9 aspect ratio images -->
                                                <div class="grid grid-cols-2 gap-2 mt-3">
                                                    <!-- Product 1 -->
                                                    <div class="welcome-card border border-gray-200 dark:border-gray-700 rounded-lg p-2">
                                                        <div class="aspect-video bg-gray-100 dark:bg-gray-700 rounded overflow-hidden mb-2">
                                                            <img src="./images/Premium Chef Knife Set.jpg" alt="Chef Knife Set" class="w-full h-full object-cover">
                                                        </div>
                                                        <div class="text-xs font-semibold welcome-product-title">Premium Chef Knife Set</div>
                                                        <div class="welcome-product-price text-xs font-bold">89.99 JOD</div>
                                                        <div class="stars-text text-xs">★★★★★</div>
                                                    </div>

                                                    <!-- Product 2 -->
                                                    <div class="welcome-card border border-gray-200 dark:border-gray-700 rounded-lg p-2">
                                                        <div class="aspect-video bg-gray-100 dark:bg-gray-700 rounded overflow-hidden mb-2">
                                                            <img src="./images/Personalized Cutting Board.jpg" alt="Cutting Board" class="w-full h-full object-cover">
                                                        </div>
                                                        <div class="text-xs font-semibold welcome-product-title">Personalized Cutting Board</div>
                                                        <div class="welcome-product-price text-xs font-bold">49.99 JOD</div>
                                                        <div class="stars-text text-xs">★★★★☆</div>
                                                    </div>
                                                </div>

                                                <div class="mt-2">
                                                    <a href="{{ url('/chatbot') }}" class="text-xs primary-text hover:underline">See all 6 recommendations →</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border-t border-gray-200 dark:border-gray-700 p-4">                                    <div class="flex">
                                        <input type="text" id="hero-demo-input" placeholder="Ask for product recommendations..." class="flex-1 rounded-l-lg border-gray-300 dark:border-gray-700 welcome-card dark:text-white px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                                        <button onclick="tryHeroDemo()" class="ml-2 rounded-r-lg welcome-button-primary px-4 py-2 text-white hover:bg-indigo-700 flex items-center justify-center">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Partners Section -->
                <section class="py-12 px-6 md:px-10 border-b border-gray-200 dark:border-gray-700">
                    <div class="max-w-7xl mx-auto">
                        <h2 class="text-center text-lg font-semibold welcome-card-title mb-8">Our Partners</h2>
                        <div class="flex items-center justify-center space-x-12">
                            <!-- AliExpress Partner -->
                            <div class="flex items-center space-x-3">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3b/Aliexpress_logo.svg/306px-Aliexpress_logo.svg.png" alt="AliExpress" class="h-8 w-auto opacity-70">
                                <span class="text-sm font-medium welcome-card-description">Active Partner</span>
                            </div>

                            <!-- Amazon Coming Soon -->
                            <div class="flex items-center space-x-3">
                                <img src="https://bulldogdm.com/bulldogdmorg/wp-content/uploads/2021/05/amazon.png" alt="Amazon" class="h-20 w-auto opacity-50">
                                <span class="text-sm font-medium text-gray-400">Coming Soon</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Features Section -->
                <section id="features" class="py-16 welcome-features-section px-6 md:px-10">
                    <div class="max-w-7xl mx-auto">
                        <h2 class="text-3xl font-bold text-center welcome-card-title">Why Shop with AI?</h2>
                        <p class="mt-4 text-xl text-center welcome-card-description max-w-3xl mx-auto">Our AI-powered shopping assistant helps you find exactly what you're looking for.</p>

                        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="welcome-card rounded-xl shadow-md p-6">
                                <div class="h-12 w-12 rounded-md welcome-chatbot-message flex items-center justify-center primary-text mb-4">
                                    <i class="fas fa-search fa-lg"></i>
                                </div>
                                <h3 class="text-xl font-semibold welcome-card-title">Natural Language Search</h3>
                                <p class="mt-4 welcome-card-description">Simply describe what you need in everyday language, and our AI will understand your request.</p>
                            </div>

                            <div class="welcome-card rounded-xl shadow-md p-6">
                                <div class="h-12 w-12 rounded-md welcome-chatbot-message flex items-center justify-center primary-text mb-4">
                                    <i class="fas fa-thumbs-up fa-lg"></i>
                                </div>
                                <h3 class="text-xl font-semibold welcome-card-title">Personalized Recommendations</h3>
                                <p class="mt-4 welcome-card-description">Get tailored product suggestions based on your preferences and requirements.</p>
                            </div>

                            <div class="welcome-card rounded-xl shadow-md p-6">
                                <div class="h-12 w-12 rounded-md welcome-chatbot-message flex items-center justify-center primary-text mb-4">
                                    <i class="fas fa-bolt fa-lg"></i>
                                </div>
                                <h3 class="text-xl font-semibold welcome-card-title">Instant Results</h3>
                                <p class="mt-4 welcome-card-description">Save time with immediate product recommendations and no more endless browsing.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- How It Works Section -->
                <section id="how-it-works" class="py-16 px-6 md:px-10">
                    <div class="max-w-7xl mx-auto">
                        <h2 class="text-3xl font-bold text-center welcome-card-title">How It Works</h2>
                        <p class="mt-4 text-xl text-center welcome-card-description max-w-3xl mx-auto">Get the perfect product recommendations in three simple steps.</p>

                        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="text-center">
                                <div class="h-16 w-16 rounded-full primary-bg text-white text-2xl flex items-center justify-center mx-auto mb-4">1</div>
                                <h3 class="text-xl font-semibold welcome-card-title">Describe What You Need</h3>
                                <p class="mt-4 welcome-card-description">Tell our AI what you're looking for, whether it's a gift idea or a specific product.</p>
                            </div>

                            <div class="text-center">
                                <div class="h-16 w-16 rounded-full primary-bg text-white text-2xl flex items-center justify-center mx-auto mb-4">2</div>
                                <h3 class="text-xl font-semibold welcome-card-title">Get Instant Recommendations</h3>
                                <p class="mt-4 welcome-card-description">Our AI analyzes your request and provides personalized product suggestions.</p>
                            </div>

                            <div class="text-center">
                                <div class="h-16 w-16 rounded-full primary-bg text-white text-2xl flex items-center justify-center mx-auto mb-4">3</div>
                                <h3 class="text-xl font-semibold welcome-card-title">Refine & Purchase</h3>
                                <p class="mt-4 welcome-card-description">Ask follow-up questions to refine results, then click through to purchase.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Example Queries Section -->
                <section id="examples" class="py-16 welcome-features-section px-6 md:px-10">
                    <div class="max-w-7xl mx-auto">
                        <h2 class="text-3xl font-bold text-center welcome-card-title">Try These Example Queries</h2>
                        <p class="mt-4 text-xl text-center welcome-card-description max-w-3xl mx-auto">See how our AI can help with different shopping needs.</p>

                        <div class="mt-12 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="welcome-card rounded-xl shadow-md p-6 hover:shadow-lg transition">
                                <h3 class="text-xl font-semibold welcome-card-title">Gift for my chef friend</h3>
                                <p class="mt-2 welcome-card-description">Find the perfect gift for someone who loves cooking.</p>
                            </div>

                            <div class="welcome-card rounded-xl shadow-md p-6 hover:shadow-lg transition">
                                <h3 class="text-xl font-semibold welcome-card-title">Spring outfit for women</h3>
                                <p class="mt-2 welcome-card-description">Discover trendy spring fashion items for women.</p>
                            </div>

                            <div class="welcome-card rounded-xl shadow-md p-6 hover:shadow-lg transition">
                                <h3 class="text-xl font-semibold welcome-card-title">Budget gaming laptop under $800</h3>
                                <p class="mt-2 welcome-card-description">Find affordable gaming laptops that fit your budget.</p>
                            </div>

                            <div class="welcome-card rounded-xl shadow-md p-6 hover:shadow-lg transition">
                                <h3 class="text-xl font-semibold welcome-card-title">Eco-friendly kitchen products</h3>
                                <p class="mt-2 welcome-card-description">Discover sustainable and environmentally-friendly kitchen items.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Call to Action -->
                <section class="py-20 px-6 md:px-10 welcome-cta-section">
                    <div class="max-w-5xl mx-auto text-center">
                        <h2 class="text-3xl font-bold text-white">Ready to transform your shopping experience?</h2>
                        <p class="mt-4 text-xl text-indigo-100">Join thousands of users who have simplified their shopping with our AI assistant.</p>
                        <div class="mt-10">
                            <a href="{{ Route::has('register') ? route('register') : '#' }}" class="inline-block rounded-lg px-6 py-3 welcome-cta-button font-medium focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-indigo-600 transition">
                                Create Free Account
                            </a>
                        </div>
                    </div>
                </section>
            </main>

            <!-- Footer -->
            <footer class="welcome-footer py-12 px-6 md:px-10">
                <div class="max-w-7xl mx-auto">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        <div>
                            <div class="flex items-center">
                                @if($siteSettings->site_logo)
                                    <img src="{{ Storage::url($siteSettings->site_logo) }}" alt="{{ $siteSettings->site_name }}" class="h-8 w-auto">
                                @else
                                    <svg class="h-8 w-8 primary-text" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                @endif
                                <span class="ml-2 text-xl font-bold welcome-card-title">{{ $siteSettings->site_name }}</span>
                            </div>
                            <p class="mt-4 welcome-footer-text">Making product discovery simple and personalized with AI.</p>
                        </div>

                        <div>
                            <h3 class="font-semibold welcome-card-title">Quick Links</h3>
                            <ul class="mt-4 space-y-2">
                                <li><a href="#features" class="welcome-footer-link hover:text-indigo-600 dark:hover:text-indigo-400">Features</a></li>
                                <li><a href="#how-it-works" class="welcome-footer-link hover:text-indigo-600 dark:hover:text-indigo-400">How It Works</a></li>
                                <li><a href="#examples" class="welcome-footer-link hover:text-indigo-600 dark:hover:text-indigo-400">Example Queries</a></li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="font-semibold welcome-card-title">Account</h3>
                            <ul class="mt-4 space-y-2">
                                @auth
                                    <li><a href="{{ url('/dashboard') }}" class="welcome-footer-link hover:text-indigo-600 dark:hover:text-indigo-400"><i class="fas fa-lightbulb mr-1"></i> My Recommendations</a></li>
                                    <li><a href="{{ url('/chatbot') }}" class="welcome-footer-link hover:text-indigo-600 dark:hover:text-indigo-400"><i class="fas fa-robot mr-1"></i> Chatbot</a></li>
                                @else
                                    <li><a href="{{ route('login') }}" class="welcome-footer-link hover:text-indigo-600 dark:hover:text-indigo-400">Login</a></li>
                                    @if (Route::has('register'))
                                        <li><a href="{{ route('register') }}" class="welcome-footer-link hover:text-indigo-600 dark:hover:text-indigo-400">Register</a></li>
                                    @endif
                                @endauth
                            </ul>
                        </div>
                    </div>

                    <div class="mt-10 pt-6 border-t border-gray-200 dark:border-gray-800 text-center welcome-footer-text">
                        <p>&copy; {{ date('Y') }} {{ $siteSettings->site_name }} AI Assistant. All rights reserved.</p>
                    </div>
                </div>
            </footer>        </div>

        <!-- Auth Modal -->
        <div id="authModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4" style="display: none;">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold welcome-card-title">Join ShopBot</h2>
                        <button onclick="closeAuthModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            <i class="fas fa-info-circle mr-2"></i>
                            Sign up to access our AI chatbot with real product recommendations from AliExpress!
                        </p>
                    </div>

                    <!-- Auth Tabs -->
                    <div class="flex mb-6 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                        <button onclick="switchAuthTab('login')" id="loginTab" class="flex-1 py-2 px-4 rounded-md text-sm font-medium transition auth-tab">
                            Login
                        </button>
                        <button onclick="switchAuthTab('register')" id="registerTab" class="flex-1 py-2 px-4 rounded-md text-sm font-medium transition auth-tab">
                            Register
                        </button>
                    </div>

                    <!-- Login Form -->
                    <div id="loginForm" class="auth-form-content">
                        <form action="{{ route('login') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium welcome-card-description mb-2">Email</label>
                                <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium welcome-card-description mb-2">Password</label>
                                <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div class="flex items-center justify-between">
                                <label class="flex items-center">
                                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                    <span class="ml-2 text-sm welcome-card-description">Remember me</span>
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500">
                                        Forgot password?
                                    </a>
                                @endif
                            </div>
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition font-medium">
                                Sign In
                            </button>
                        </form>
                    </div>

                    <!-- Register Form -->
                    <div id="registerForm" class="auth-form-content hidden">
                        <form action="{{ route('register') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium welcome-card-description mb-2">Name</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium welcome-card-description mb-2">Email</label>
                                <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium welcome-card-description mb-2">Password</label>
                                <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium welcome-card-description mb-2">Confirm Password</label>
                                <input type="password" name="password_confirmation" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            </div>
                            <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition font-medium">
                                Create Account
                            </button>
                        </form>
                    </div>

                    <div class="mt-6 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            By joining, you agree to our Terms of Service and Privacy Policy
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function showAuthModal() {
                const modal = document.getElementById('authModal');
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeAuthModal() {
                const modal = document.getElementById('authModal');
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }

            function switchAuthTab(tab) {
                const loginTab = document.getElementById('loginTab');
                const registerTab = document.getElementById('registerTab');
                const loginForm = document.getElementById('loginForm');
                const registerForm = document.getElementById('registerForm');

                if (tab === 'login') {
                    loginTab.classList.add('bg-white', 'dark:bg-gray-800', 'text-gray-900', 'dark:text-white', 'shadow-sm');
                    loginTab.classList.remove('text-gray-500', 'dark:text-gray-400');
                    registerTab.classList.remove('bg-white', 'dark:bg-gray-800', 'text-gray-900', 'dark:text-white', 'shadow-sm');
                    registerTab.classList.add('text-gray-500', 'dark:text-gray-400');

                    loginForm.classList.remove('hidden');
                    registerForm.classList.add('hidden');
                } else {
                    registerTab.classList.add('bg-white', 'dark:bg-gray-800', 'text-gray-900', 'dark:text-white', 'shadow-sm');
                    registerTab.classList.remove('text-gray-500', 'dark:text-gray-400');
                    loginTab.classList.remove('bg-white', 'dark:bg-gray-800', 'text-gray-900', 'dark:text-white', 'shadow-sm');
                    loginTab.classList.add('text-gray-500', 'dark:text-gray-400');

                    registerForm.classList.remove('hidden');
                    loginForm.classList.add('hidden');
                }
            }

            // Initialize with register tab active
            document.addEventListener('DOMContentLoaded', function() {
                switchAuthTab('register'); // Default to register to encourage signups

                // Close modal when clicking outside
                document.getElementById('authModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeAuthModal();
                    }
                });
            });
        </script>

        <style>
            .auth-tab {
                transition: all 0.2s ease;
            }
        </style>
    </body>
</html>
