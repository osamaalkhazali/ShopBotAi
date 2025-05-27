<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - {{ $siteSettings->site_name }}</title>

    <!-- Favicon with cache busting -->
    <link rel="icon" href="{{ $siteSettings->getFaviconUrl() }}?v={{ time() }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $siteSettings->getFaviconUrl() }}?v={{ time() }}" type="image/x-icon">

    <!-- Prevent favicon caching -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Adding Google Font for better typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
        }

        .sidebar-active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left: 3px solid #10b981;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hover-scale {
            transition: transform 0.2s;
        }

        .hover-scale:hover {
            transform: scale(1.02);
        }

        .stats-card {
            transition: all 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 12px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .sidebar-text {
            font-size: 0.85rem;
        }

        .sidebar-heading {
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* Custom input styles */
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="search"],
        input[type="tel"],
        input[type="url"],
        input[type="date"],
        select,
        textarea {
            height: 38px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        textarea {
            height: auto;
        }

        /* Filter button styles */
        .filter-btn {
            height: 38px;
            padding: 0 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Top Navigation -->
    <nav class="bg-gradient-to-r from-emerald-700 to-emerald-900 text-white p-3 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="lg:hidden focus:outline-none">
                    <i class="fas fa-bars text-lg"></i>
                </button>

                <a href="{{ route('admin.dashboard') }}" class="font-bold text-lg flex items-center">
                    <i class="fas fa-robot mr-2"></i>
                    <span>Chatbot Admin</span>
                </a>
            </div>

            <div class="flex items-center space-x-3">
                <div class="hidden md:flex items-center space-x-2">
                    <span class="font-medium text-sm">{{ Auth::guard('admin')->user()->name }}</span>
                    <span class="bg-emerald-600 text-xs uppercase px-2 py-0.5 rounded-full">
                        {{ Auth::guard('admin')->user()->role === 'super_admin' ? 'Super Admin' : 'Admin' }}
                    </span>
                </div>

                <div class="relative group">
                    <button class="focus:outline-none">
                        <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                    </button>

                    <div class="absolute right-0 w-44 bg-white shadow-lg rounded-md mt-2 py-2 z-20 hidden group-hover:block text-sm">
                        <a href="#" class="block px-4 py-1.5 text-gray-800 hover:bg-emerald-100">
                            <i class="fas fa-user-cog mr-2"></i> Profile
                        </a>
                        <div class="border-t border-gray-200 my-1"></div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-1.5 text-gray-800 hover:bg-emerald-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex flex-1">
        <!-- Sidebar - Reduced width from w-64 to w-52 -->
        <aside id="sidebar" class="bg-gray-800 text-white w-52 min-h-screen py-3 hidden lg:block">
            <div class="px-3 py-2">
                <p class="sidebar-heading uppercase tracking-wider text-gray-400">Main</p>
                <ul class="mt-1.5 space-y-0.5">
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                           class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-tachometer-alt mr-2 text-xs"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.users') }}"
                           class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.users*') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-users mr-2 text-xs"></i> Users
                        </a>
                    </li>
                </ul>
            </div>

            <div class="px-3 py-2 mt-3">
                <p class="sidebar-heading uppercase tracking-wider text-gray-400">AliExpress</p>
                <ul class="mt-1.5 space-y-0.5">
                    <li>
                        <a href="{{ route('admin.aliexpress.products.index') }}"
                           class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.aliexpress.products*') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-boxes mr-2 text-xs"></i> Products
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.aliexpress.categories.index') }}"
                           class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.aliexpress.categories*') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-tag mr-2 text-xs"></i> Categories
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.aliexpress.chat_sessions.index') }}"
                           class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.aliexpress.chat_sessions*') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-comments mr-2 text-xs"></i> Chat Sessions
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.aliexpress.api.index') }}"
                           class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.aliexpress.api*') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-cogs mr-2 text-xs"></i> API Test
                        </a>
                    </li>
                </ul>
            </div>

            <div class="px-3 py-2 mt-3">
                <p class="sidebar-heading uppercase tracking-wider text-gray-400">Amazon Bot</p>
                <ul class="mt-1.5 space-y-0.5">
                    <li>
                        <a href="{{ route('admin.products.index') }}"
                           class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.products*') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-boxes mr-2 text-xs"></i> Products
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.categories.index') }}"
                           class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.categories*') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-tag mr-2 text-xs"></i> Categories
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.chat_sessions.index') }}"
                           class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.chat_sessions*') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-comments mr-2 text-xs"></i> Chat Sessions
                        </a>
                    </li>
                </ul>
            </div>

            @if(Auth::guard('admin')->user()->role === 'super_admin')
            <div class="px-3 py-2 mt-3">
                <p class="sidebar-heading uppercase tracking-wider text-gray-400">Administration</p>
                <ul class="mt-1.5 space-y-0.5">
                    <li>
                        <a href="{{ route('admin.admins') }}"
                           class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.admins*') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-user-shield mr-2 text-xs"></i> Manage Admins
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.index') }}"
                           class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.settings*') ? 'sidebar-active' : '' }}">
                            <i class="fas fa-cog mr-2 text-xs"></i> Site Settings
                        </a>
                    </li>
                </ul>
            </div>
            @endif
        </aside>

        <!-- Main content -->
        <main class="flex-1 p-4 overflow-x-hidden">
            <!-- Mobile sidebar backdrop -->
            <div id="sidebar-backdrop" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-20 lg:hidden hidden"></div>

            <!-- Mobile sidebar - Reduced width from w-64 to w-52 -->
            <div id="mobile-sidebar" class="fixed inset-y-0 left-0 w-52 bg-gray-800 text-white z-30 transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden">
                <div class="p-3 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <span class="text-base font-semibold">Menu</span>
                        <button id="close-sidebar" class="text-white focus:outline-none">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="px-3 py-2">
                    <p class="sidebar-heading uppercase tracking-wider text-gray-400">Main</p>
                    <ul class="mt-1.5 space-y-0.5">
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                               class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.dashboard') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-tachometer-alt mr-2 text-xs"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users') }}"
                               class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.users*') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-users mr-2 text-xs"></i> Users
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="px-3 py-2 mt-3">
                    <p class="sidebar-heading uppercase tracking-wider text-gray-400">AliExpress</p>
                    <ul class="mt-1.5 space-y-0.5">
                        <li>
                            <a href="{{ route('admin.aliexpress.products.index') }}"
                               class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.aliexpress.products*') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-boxes mr-2 text-xs"></i> Products
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.aliexpress.categories.index') }}"
                               class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.aliexpress.categories*') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-tag mr-2 text-xs"></i> Categories
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.aliexpress.chat_sessions.index') }}"
                               class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.aliexpress.chat_sessions*') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-comments mr-2 text-xs"></i> Chat Sessions
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.aliexpress.api.index') }}"
                               class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.aliexpress.api*') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-cogs mr-2 text-xs"></i> API Test
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="px-3 py-2 mt-3">
                    <p class="sidebar-heading uppercase tracking-wider text-gray-400">Amazon Bot</p>
                    <ul class="mt-1.5 space-y-0.5">
                        <li>
                            <a href="{{ route('admin.products.index') }}"
                               class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.products*') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-boxes mr-2 text-xs"></i> Products
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.categories.index') }}"
                               class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.categories*') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-tag mr-2 text-xs"></i> Categories
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.chat_sessions.index') }}"
                               class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.chat_sessions*') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-comments mr-2 text-xs"></i> Chat Sessions
                            </a>
                        </li>
                    </ul>
                </div>

                @if(Auth::guard('admin')->user()->role === 'super_admin')
                <div class="px-3 py-2 mt-3">
                    <p class="sidebar-heading uppercase tracking-wider text-gray-400">Administration</p>
                    <ul class="mt-1.5 space-y-0.5">
                        <li>
                            <a href="{{ route('admin.admins') }}"
                               class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.admins*') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-user-shield mr-2 text-xs"></i> Manage Admins
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.index') }}"
                               class="sidebar-text block px-3 py-1.5 rounded-md hover:bg-gray-700 transition-all {{ request()->routeIs('admin.settings*') ? 'sidebar-active' : '' }}">
                                <i class="fas fa-cog mr-2 text-xs"></i> Site Settings
                            </a>
                        </li>
                    </ul>
                </div>
                @endif
            </div>

            <!-- Breadcrumbs -->
            <div class="bg-white rounded-lg shadow-sm p-3 mb-4 text-sm">
                <div class="flex items-center text-gray-600">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600">
                        <i class="fas fa-home"></i>
                    </a>
                    <span class="mx-2">/</span>
                    @yield('breadcrumbs')
                </div>
            </div>

            <!-- Flash messages -->
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 rounded fade-in text-sm" role="alert">
                    <div class="flex items-center">
                        <div class="py-1">
                            <i class="fas fa-check-circle mr-2"></i>
                        </div>
                        <div>
                            <p>{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded fade-in text-sm" role="alert">
                    <div class="flex items-center">
                        <div class="py-1">
                            <i class="fas fa-times-circle mr-2"></i>
                        </div>
                        <div>
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <div class="fade-in">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const closeSidebarButton = document.getElementById('close-sidebar');
            const sidebar = document.getElementById('mobile-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');

            mobileMenuButton.addEventListener('click', function() {
                sidebar.classList.toggle('-translate-x-full');
                backdrop.classList.toggle('hidden');
                document.body.classList.toggle('overflow-hidden');
            });

            closeSidebarButton.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            });

            backdrop.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
